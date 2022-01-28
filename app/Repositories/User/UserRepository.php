<?php

namespace App\Repositories\User;

use App\Models\User;
use Infobip\Api\TfaApi;
use App\Enums\StatusCodeEnum;
use Infobip\Model\TfaPinType;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;
use Infobip\Model\TfaVerifyPinRequest;
use Infobip\Model\TfaApplicationRequest;
use Infobip\Model\TfaCreateMessageRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Infobip\Model\TfaStartAuthenticationRequest;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class UserRepository implements UserInterface
{
    protected $user;
    protected $infobip_service;
    protected $client;
    protected $faApi;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->infobip_service = new \Infobip\Configuration();
        $this->infobip_service->setHost(env('URL_BASE_PATH'));
        $this->infobip_service->setApiKeyPrefix('Authorization', env('API_KEY_PREFIX'));
        $this->infobip_service->setApiKey('Authorization', env('API_KEY'));
        $this->client = new \GuzzleHttp\Client();
        $this->tfaApi = new TfaApi($this->client, $this->infobip_service);
    }

    public function register(array $request)
 	{
 		$user = $this->user->create([
            'name' => $request['name'],
            'username' => $request['username'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        return $data = [
            'access_token' => JWTAuth::fromUser($user),
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl')
        ];
 	}

    public function login(array $request)
    {
        try {
                if (! $token = JWTAuth::attempt($request)) {
                    throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'Invalid credentials');
                }
            } catch (JWTException $e) {
                throw new CustomException(StatusCodeEnum::SERVER_ERROR, null, $e);
            }

        return $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl')
        ];
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                throw new CustomException(StatusCodeEnum::NOT_FOUND, null, 'User not found');
            }

        } catch (TokenExpiredException $e) {
                throw new CustomException($e->getStatusCode(), null, 'Token expired');

        } catch (TokenInvalidException $e) {
                throw new CustomException($e->getStatusCode(), null, 'Token invalid');

        } catch (JWTException $e) {
                throw new CustomException($e->getStatusCode(), null, 'Token absent');
        }

        return $user;
    }

    public function resetPassword(array $request) :bool
    {
        $user = $this->user->whereEmail($request['email'])->first();
        if($user){
            $user->password = Hash::make($request['password']);
            $user->save();
            
            return true;
        }
        
        throw new CustomException(StatusCodeEnum::NOT_FOUND, null, 'User not found');
    }

    public function generateVerificationPin(string $phone_number, string $id) :bool
    {
        $appId = $this->getInfobipApplicationId($this->tfaApi);
        $messageId = $this->getInfobipMessageId($this->tfaApi, $appId);

        $sendCodeResponse = $this->tfaApi->sendTfaPinCodeOverSms(true,
            (new TfaStartAuthenticationRequest())
                ->setApplicationId($appId)
                ->setMessageId($messageId)
                ->setFrom("ServiceSMS")
                ->setTo($phone_number));

        if($sendCodeResponse->getSmsStatus() == "MESSAGE_SENT"){
            $user = $this->user->find($id);
            $user->phone_number = $phone_number;
            $user->pin_id = $sendCodeResponse->getPinId();
            $user->save();
            return true;
        }
        
        throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'Pin not sent');
    }

    public function verifySecuredAuthenticationPin(string $pinId, string $pin) :bool
    {
        $verifyResponse = $this->tfaApi->verifyTfaPhoneNumber($pinId, (new TfaVerifyPinRequest())->setPin($pin));
        if($verifyResponse->getVerified()){
            $user = $this->user->where('pin_id', $pinId)->first();
            $user->verify_phone_number = true;
            $user->save();

            return true;
        }

        throw new CustomException(StatusCodeEnum::BAD_REQUEST, null, 'Incorrect Pin');
    }

    private function getInfobipApplicationId(object $tfaApi) :string
    {
        $tfaApplication = $tfaApi->createTfaApplication(
            (new TfaApplicationRequest())->setName("2FA application"));

        return $tfaApplication->getApplicationId();
    }

    private function getInfobipMessageId(object $tfaApi, string $appId) :string
    {
        $tfaMessageTemplate = $tfaApi->createTfaMessageTemplate($appId,
            (new TfaCreateMessageRequest())
                ->setMessageText("Your pin is {{pin}}")
                ->setPinType(TfaPinType::NUMERIC)
                ->setPinLength(4));
        return $tfaMessageTemplate->getMessageId();
    }
}
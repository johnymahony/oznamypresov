<?php

namespace App\Http\Requests;

use App\Cityzen;
use App\CookieId;
use Illuminate\Foundation\Http\FormRequest;

class SubscribeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'agree' => 'required',
            't'     => 'required',
        ];
    }

    public function messages()
    {
        return [
            'agree.required' => 'Musíte súhlasiť s podmienkami',
            'email.required' => 'Musíte zadať svoju emailovú adresu',
            'email.email'    => 'Neplatný formát emailovej adresy',
        ];
    }

    public function handle()
    {
        $diff = time() - $this->t;
        if ($diff <= 3 || request()->x) {
            return 'spam';
        }

        /** @var Cityzen $cityzen */
        $cityzen = Cityzen::whereEmail($this->email)->first();
        $data    = [
            'hash'       => md5(microtime()),
            'hash_until' => dateTimeNow('+15 minutes'),
        ];
        if (empty($cityzen)) {
            $data['radius'] = 500;
            $data['email']  = $this->email;
            $cityzen        = Cityzen::create($data);
            $created        = true;
        } else {
            $created = false;
            $cityzen->update($data);
        }
        $cityzen->sendEmail('welcome');
        CookieId::setCityzen($cityzen->id);

        return $created;
    }
}

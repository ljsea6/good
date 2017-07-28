<?php
namespace App\Http\Requests\Redes;

use App\Http\Requests\Request;
use Illuminate\Routing\Route;

class Nuevared extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool


     */
    protected $route;

    public function __construct(Route $route) {
        $this->route = $route;
    }

    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'string|required',
            'frontal_amount' =>'numeric|required',
            'frontal_amount'  => 'numeric|required',
        ];
    }


}

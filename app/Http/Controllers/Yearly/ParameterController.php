<?php

namespace App\Http\Controllers\Yearly;

use App\Http\Controllers\Controller;
use App\Models\Yearly\Parameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ParameterController extends Controller
{
    public function index(){
        if (Gate::denies('parameters')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/Parameters/Parameters', [
            'parameters' => getParameters(),
            'permissions' => getPermissions()
        ]);
    }

    private function saveForm(Request $request, $id = 0)
    {
        if (Gate::denies('parameters-modify')) {
            return deny();
        }

        $this->validateForm($request);
        foreach ($request->parameters as $para) {
            $parameter = Parameter::findOrFail($para['id']);
            $parameter->para_value = $para['para_value'];
            $parameter->save();
        }
        return reply(true, [
            'parameter' => $parameter,
        ]);
    }

    private function validateForm(Request $request)
    {
        $rules = [];
        $messages = [];
        $i = 1;
        foreach ($request->parameters as $key => $para) {
            if ($para['mandatory'] == 'Yes') {
                $rules += [
                    'parameters.' . $key . '.para_value' => 'required|max:250',
                ];
                $messages = [
                    'parameters.*.para_value.required' => 'Parameter is required',
                ];
                $i++;
            }
        }

        $this->validate($request, $rules, $messages);
    }

    public function store(Request $request)
    {
        return $this->saveForm($request);
    }
}

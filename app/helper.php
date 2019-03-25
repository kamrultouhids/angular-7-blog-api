<?php

    function apiResponse($status, $message , $values = null)
    {
        $data = [
            'result' => $status,
            'msg' => $message,
            'data' => $values
        ];
        return response()->json($data, 200);
    }


    function  apiValidateError($errorArray)
    {
        $errors = $errorArray->toArray();
        $errorArray = [];
        foreach ($errors as $key => $value)
        {
            $errorArray[$key] = $value[0];
        }
        return $errorArray;
    }
<?php

namespace App\Http\Controllers;

use App\Models\Country;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::all();

        return response()->json($countries);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $country = Country::where('code', $id)->first();
        if (!$country) return response()->json(['message' => 'No se encontró el país'], 404);

        return response()->json(['name' => $country->name]);
    }
}

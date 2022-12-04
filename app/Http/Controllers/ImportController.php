<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AppRevalIHPBImport; //This is needed for Laravel-Excel.
use App\Models\User;  //Our user model.
use App\Models\CsvData; //Our model for csv.
use App\Http\Requests\CsvImportRequest; //I'll share this in a sec.
use Maatwebsite\Excel\Facades\Excel; //This is from Laravel-Excel
use Illuminate\Support\Str; //This is for basic password creation


class ImportController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    public function form()
    {
        exit('test');
        return view('import.form');
    }

    public function parseImport(CsvImportRequest $request)
    {
        //we getting with the request the file. So you need to create request with
        //Laravel. And you should add this to your Controller as use App\Http\Requests\CsvImportRequest;

        $path = $request->file('csv_file')->getRealPath();
        if ($request->has('header')) {
            //this is coming from Laravel-Excel package. Make sure you added to your
            //controller; use Maatwebsite\Excel\Facades\Excel;
            $data = Excel::toArray(new AppRevalIHPBImport, request()->file('csv_file'))[0];
        } else {
            $data = array_map('str_getcsv', file($path));
        }

        if (count($data) > 0) {
            //checking if the header option selected
            if ($request->has('header')) {
                $csv_header_fields = [];
                foreach ($data[0] as $key => $value) {
                    $csv_header_fields[] = $key;
                }
            }
            $csv_data = array_slice($data, 0, 2);
            //creating csvdata for our database
            $csv_data_file = CsvData::create([
                'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
                'csv_header' => $request->has('header'),
                'csv_data' => json_encode($data)
            ]);
        } else {
            return redirect()->back();
        }
        //this is the view when we go after we submit our form.We're sending our data so we can select to match with db_fields.
        return view('import.fields', compact('csv_header_fields', 'csv_data', 'csv_data_file'));
    }

    public function processImport(Request $request)
    { //we are getting data from request to match the fields.
        $data = CsvData::find($request->csv_data_file_id);
        $csv_data = json_decode($data->csv_data, true);
        $request->fields = array_flip($request->fields);
        foreach ($csv_data as $row) {
            $contact = new User();
            foreach (config('app.db_fields') as $index => $field) {
                //using config app.db_fields while matching with request fields
                if ($data->csv_header) {
                    if ($field == "null") {
                        continue;
                    } else if ($field == "password") {
                        //this is checkin if password option is set. If not, it is creating a password. You can eliminate this according to your needs.
                        if (isset($request->fields['password'])) {
                            $pw = $row[$request->fields['password']];
                        } else {
                            $pw = Str::random(10);
                        }
                    } else
                        $contact->$field = $row[$request->fields[$field]];
                } else
                //same with the if but without headers. You can create a function to avoid writing
                //codes twice.
                {
                    if ($field == "null") {
                        continue;
                    } else if ($field == "password") {
                        if (isset($request->fields['password'])) {
                            $pw = $row[$request->fields['password']];
                        } else {
                            $pw = Str::random(10);
                        }
                    } else
                        $contact->$field = $row[$request->fields[$index]];
                }
            }
            $user = User::where(['email' => $contact->email])->first();
            //checking for duplicate
            if (empty($user)) {
                $contact->password = bcrypt($pw);
                $contact->save();
            } else {
                $duplicated[] = $contact->email;
                //if you want you can keep the duplicated ones to check which ones are duplicated
            }
        }
        //you can redirect wherever you want. I didn't need an success view so I returned
        //voyagers original users view to see my data.
        return redirect(route('voyager.users.index'));
    }
}

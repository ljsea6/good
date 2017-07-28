<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\Archivo;

class FilesController extends Controller {

	public function index() {
		return view('admin.files.index', compact('tipo_id','id'));
	}

	public function postUploads(){
       /*/ ini_set('upload_max_filesize', '10M');
           ini_set('post_max_size', '10M');
           if (!empty($_FILES))
        {
            $config['upload_path'] = './uploads/recived_files/files';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = '2000';


            $this->load->library('upload', $config);
            if ($this->upload->do_upload())
            {   //success
                $data = array('upload_data' => $this->upload->data());
            }
 //obtenemos el campo file definido en el formulario
       
                redirect("/home");
	}
*/

	if(!\Input::file("file"))
    {
        return redirect('uploads')->with('error-message', 'File has required field');
    }
 
    $mime = \Input::file('file')->getMimeType();
    $extension = strtolower(\Input::file('file')->getClientOriginalExtension());
    $fileName = uniqid().'.'.$extension;
    $path = "files_uploaded";
 
    switch ($mime)
    {
        case "image/jpeg":
        case "image/png":
        case "image/gif":
        case "application/pdf":
            if (\Request::file('file')->isValid())
            {
                \Request::file('file')->move($path, $fileName);
                $upload = new Archivo();
                $upload->filename = $fileName;
                if($upload->save())
                {
                    return redirect('uploads')->with('success-message', 'File has been uploaded');
                }
                else
                {
                    \File::delete($path."/".$fileName);
                    return redirect('uploads')->with('error-message', 'An error ocurred saving data into database');
                }
            }
        break;
        default:
            return redirect('uploads')->with('error-message', 'Extension file is not valid');
    }
    }
        public function Uploads_init(Request $request) {
		//return $request;
		//return $request->size;
		//return currentUser()->id;
		$archivo = new Archivo;
		$archivo->usuario_id = currentUser()->id;
		$archivo->nombre 	= $request->name;
		$archivo->tamano	= $request->size;
		$archivo->tipo_ext	= $request->type;
		$archivo->tipo_id   = $request->tipo_id;
        $archivo->save();
	}
        
}

	

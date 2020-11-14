<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('DAO');
    }

    // curl -X GET http://localhost/api_escuela/index.php/curso/Api/cursos?pid=2 | jq
    public function cursos_get()
    {
        if ($this->get('pid')) {
            $result = $this->DAO->selectEntity('curso', array('curso_id' => $this->get('pid')), true);
        } else {
            $result = $this->DAO->selectEntity('curso');
        }
        $response = array(
            "status" => 200,
            "status_text" => "success",
            "api" => "curso/api/cursos",
            "method" => "GET",
            "message" => "Listado de cursos",
            "data" => $result,
        );
        $this->response($response, 200);
    }

    public function cursos_post()
    {
        $this->form_validation->set_data($this->post());

        $this->form_validation->set_rules('pNombre', 'Nombre', 'required|max_length[45]');
        $this->form_validation->set_rules('pDuracion', 'Duracion', 'required|numeric');
        if ($this->form_validation->run()) {
            $data = array(
                'nombre_curso' => $this->post('pNombre'),
                'duracion_curso' => $this->post('pDuracion'),
            );
            $this->DAO->saveOrUpdate('curso', $data);
            $response = array(
                "status" => 200,
                "status_text" => "succes",
                "api" => "curso/api/cursos",
                "method" => "POST",
                "message" => "Registro correcto",
                "data" => null,
            );

        } else {
            $response = array(
                "status" => 500,
                "status_text" => "error",
                "api" => "curso/api/cursos",
                "method" => "POST",
                "message" => "Error al registrar el curso",
                "errors" => $this->form_validation->error_array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

    // curl -d '{"pNombre" : "Matematicas","pDuracion" : 3}' -X PUT http://localhost/api_escuela/index.php/curso/Api/cursos/pid/2 | jq
    public function cursos_put()
    {
        if ($this->get('pid')) {
            $curso_exists = $this->DAO->selectEntity('curso', array('curso_id' => $this->get('pid')), true);
            if ($curso_exists) {
                $this->form_validation->set_data($this->put());

                $this->form_validation->set_rules('pNombre', 'Nombre', 'required|max_length[45]');
                $this->form_validation->set_rules('pDuracion', 'Duracion', 'required|numeric');

                if ($this->form_validation->run()) {
                    $data = array(
                        'nombre_curso' => $this->post('pNombre'),
                        'duracion_curso' => $this->post('pDuracion'),
                    );
                    $this->DAO->saveOrUpdate('curso', $data, array('curso_id' => $this->post('pid')));

                    $response = array(
                        "status" => 200,
                        "status_text" => "succes",
                        "api" => "curso/api/cursos",
                        "method" => "PUT",
                        "message" => "Curso actualizado correctamente",
                        "data" => null,
                    );
                } else {
                    $response = array(
                        "status" => 500,
                        "status_text" => "error",
                        "api" => "curso/api/cursos",
                        "method" => "PUT",
                        "message" => "Error al actualizar el curso",
                        "errors" => $this->form_validation->error_array(),
                        "data" => null,
                    );
                }

            } else {
                $response = array(
                    "status" => 404,
                    "status_text" => "error",
                    "api" => "curso/api/cursos",
                    "method" => "PUT",
                    "message" => "Curso no localizado",
                    "errors" => array(),
                    "data" => null,
                );
            }
        } else {
            $response = array(
                "status" => 404,
                "status_text" => "error",
                "api" => "curso/api/cursos",
                "method" => "PUT",
                "message" => "Identificador no localizado, La clave de curso no fue enviada",
                "errors" => array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

    public function cursos_delete()
    {
        if ($this->get('pid')) {
            $curso_exists = $this->DAO->selectEntity('curso', array('curso_id' => $this->get('pid')), true);
            if ($curso_exists) {
                $this->DAO->deleteItemEntity('curso', array('curso_id' => $this->get('pid')));
                $response = array(
                    "status" => 200,
                    "status_text" => "succes",
                    "api" => "curso/api/cursos",
                    "method" => "DELETE",
                    "message" => "Curso borrado correctamente",
                    "data" => null,
                );
            } else {
                $response = array(
                    "status" => 404,
                    "status_text" => "error",
                    "api" => "curso/api/cursos",
                    "method" => "DELETE",
                    "message" => "Curso no localizado",
                    "errors" => array(),
                    "data" => null,
                );
            }
        } else {
            $response = array(
                "status" => 404,
                "status_text" => "error",
                "api" => "curso/api/cursos",
                "method" => "DELETE",
                "message" => "Identificador no localizado, La clave de curso no fue enviada",
                "errors" => array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

}

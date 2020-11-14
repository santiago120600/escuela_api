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

    // curl -X GET http://localhost/api_escuela/index.php/estudiante/Api/estudiantes?pid=2 | jq
    public function estudiantes_get()
    {
        if ($this->get('pid')) {
            $result = $this->DAO->selectEntity('estudiante', array('estudiante_id' => $this->get('pid')), true);
        } else {
            $result = $this->DAO->selectEntity('estudiante');
        }
        $response = array(
            "status" => 200,
            "status_text" => "success",
            "api" => "estudiante/api/estudiantes",
            "method" => "GET",
            "message" => "Listado de estudiantes",
            "data" => $result,
        );
        $this->response($response, 200);
    }

    public function estudiantes_post()
    {
        $this->form_validation->set_data($this->post());

        $this->form_validation->set_rules('pNombre', 'Nombre', 'required|max_length[45]');
        $this->form_validation->set_rules('pEmail', 'Email', 'required|max_length[45]');
        if ($this->form_validation->run()) {
            $data = array(
                'nombre_estudiante' => $this->post('pNombre'),
                'email_estudiante' => $this->post('pEmail'),
            );
            $this->DAO->saveOrUpdate('estudiante', $data);
            $response = array(
                "status" => 200,
                "status_text" => "succes",
                "api" => "estudiante/api/estudiantes",
                "method" => "POST",
                "message" => "Registro correcto",
                "data" => null,
            );

        } else {
            $response = array(
                "status" => 500,
                "status_text" => "error",
                "api" => "estudiante/api/estudiantes",
                "method" => "POST",
                "message" => "Error al registrar el estudiante",
                "errors" => $this->form_validation->error_array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

    public function estudiantes_put()
    {
        if ($this->get('pid')) {
            $curso_exists = $this->DAO->selectEntity('estudiante', array('estudiante_id' => $this->get('pid')), true);
            if ($curso_exists) {
                $this->form_validation->set_data($this->put());

                $this->form_validation->set_rules('pNombre', 'Nombre', 'required|max_length[45]');
                $this->form_validation->set_rules('pEmail', 'Email', 'required|max_length[45]');

                if ($this->form_validation->run()) {
                    $data = array(
                        'nombre_estudiante' => $this->post('pNombre'),
                        'email_estudiante' => $this->post('pEmail'),
                    );
                    $this->DAO->saveOrUpdate('estudiante', $data, array('estudiante_id' => $this->post('pid')));

                    $response = array(
                        "status" => 200,
                        "status_text" => "succes",
                        "api" => "estudiante/api/estudiantes",
                        "method" => "PUT",
                        "message" => "Estudiante actualizado correctamente",
                        "data" => null,
                    );
                } else {
                    $response = array(
                        "status" => 500,
                        "status_text" => "error",
                        "api" => "estudiante/api/estudiantes",
                        "method" => "PUT",
                        "message" => "Error al actualizar el estudiante",
                        "errors" => $this->form_validation->error_array(),
                        "data" => null,
                    );
                }

            } else {
                $response = array(
                    "status" => 404,
                    "status_text" => "error",
                    "api" => "estudiante/api/estudiantes",
                    "method" => "PUT",
                    "message" => "Estudiante no localizado",
                    "errors" => array(),
                    "data" => null,
                );
            }
        } else {
            $response = array(
                "status" => 404,
                "status_text" => "error",
                "api" => "estudiante/api/estudiantes",
                "method" => "PUT",
                "message" => "Identificador no localizado, La clave de estudiante no fue enviada",
                "errors" => array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

    public function estudiantes_delete()
    {
        if ($this->get('pid')) {
            $curso_exists = $this->DAO->selectEntity('estudiante', array('estudiante_id' => $this->get('pid')), true);
            if ($curso_exists) {
                $this->DAO->deleteItemEntity('estudiante', array('estudiante_id' => $this->get('pid')));
                $response = array(
                    "status" => 200,
                    "status_text" => "succes",
                    "api" => "estudiante/api/estudiantes",
                    "method" => "DELETE",
                    "message" => "Estudiante borrado correctamente",
                    "data" => null,
                );
            } else {
                $response = array(
                    "status" => 404,
                    "status_text" => "error",
                    "api" => "estudiante/api/estudiantes",
                    "method" => "DELETE",
                    "message" => "Estudiante no localizado",
                    "errors" => array(),
                    "data" => null,
                );
            }
        } else {
            $response = array(
                "status" => 404,
                "status_text" => "error",
                "api" => "estudiante/api/estudiantes",
                "method" => "DELETE",
                "message" => "Identificador no localizado, La clave de estudiante no fue enviada",
                "errors" => array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

}

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

    public function matricula_get()
    {
        if ($this->get('pid')) {
            $result = $this->DAO->selectEntity('matricula_view', array('curso_id' => $this->get('pid')), true);
        } else {
            $result = $this->DAO->selectEntity('matricula_view');
        }
        $response = array(
            "status" => 200,
            "status_text" => "success",
            "api" => "matricula/api/matricula",
            "method" => "GET",
            "message" => "Listado de matriculas",
            "data" => $result,
        );
        $this->response($response, 200);
    }

    public function matricula_post()
    {
        $this->form_validation->set_data($this->post());

        $this->form_validation->set_rules('pCurso', 'Clave de curso', 'required|callback_valid_curso');
        $this->form_validation->set_rules('pEstudiante', 'Clave de estudiante', 'required|callback_valid_estudiante');
        if ($this->form_validation->run()) {
            $data = array(
                'curso_fk' => $this->post('pCurso'),
                'estudiante_fk' => $this->post('pEstudiante'),
            );
            $this->DAO->saveOrUpdate('matricula', $data);
            $response = array(
                "status" => 200,
                "status_text" => "succes",
                "api" => "matricula/api/matricula",
                "method" => "POST",
                "message" => "Registro correcto",
                "data" => null,
            );

        } else {
            $response = array(
                "status" => 500,
                "status_text" => "error",
                "api" => "matricula/api/matricula",
                "method" => "POST",
                "message" => "Error al registrar matricula",
                "errors" => $this->form_validation->error_array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

    public function valid_estudiante($value)
    {
        if ($value) {
            $disciplina_exists = $this->DAO->selectEntity('estudiante', array('estudiante_id' => $value), true);
            if ($disciplina_exists) {
                return true;
            } else {
                $this->form_validation->set_message('valid_estudiante', 'La clave del campo {field} no es correcto');
                return false;
            }
        } else {
            $this->form_validation->set_message('valid_estudiante', 'El campo {field} es requerido');
            return false;
        }
    }

    public function valid_curso($value)
    {
        if ($value) {
            $curso_exists = $this->DAO->selectEntity('curso', array('curso_id' => $value), true);
            if ($curso_exists) {
                return true;
            } else {
                $this->form_validation->set_message('valid_curso', 'La clave del campo {field} no es correcto');
                return false;
            }
        } else {
            $this->form_validation->set_message('valid_curso', 'El campo {field} es requerido');
            return false;
        }
    }

    // puede modificar el curso si es que se equivoco para cambiar de curso al estudiante
    // no puede modificar el estudiante
    public function matricula_put()
    {
        if ($this->get('idcurso') && $this->get('idest')) {
            $curso_exists = $this->DAO->selectEntity('curso', array('curso_id' => $this->get('idcurso')), true);
            $estudiante_exists = $this->DAO->selectEntity('estudiante', array('estudiante_id' => $this->get('idest')), true);

            if ($curso_exists) {
                if ($estudiante_exists) {
                    $this->form_validation->set_data($this->put());
    
                    $this->form_validation->set_rules('pCurso', 'Clave de curso', 'required|callback_valid_curso');
    
                    if ($this->form_validation->run()) {
                        $data = array(
                            'curso_fk' => $this->put('pCurso')
                        );
                        $this->DAO->saveOrUpdate('matricula', $data, array('estudiante_fk' => $this->post('idest'),'curso_fk' => $this->post('idcurso')));
    
                        $response = array(
                            "status" => 200,
                            "status_text" => "succes",
                            "api" => "matricula/api/matricula",
                            "method" => "PUT",
                            "message" => "Curso-disciplina actualizado correctamente",
                            "data" => null,
                        );
                    } else {
                        $response = array(
                            "status" => 500,
                            "status_text" => "error",
                            "api" => "matricula/api/matricula",
                            "method" => "PUT",
                            "message" => "Error al actualizar el curso-disciplina",
                            "errors" => $this->form_validation->error_array(),
                            "data" => null
                        );
                    }
                }else{
                    $response = array(
                        "status" => 404,
                        "status_text" => "error",
                        "api" => "matricula/api/matricula",
                        "method" => "PUT",
                        "message" => "Estudiante no localizado",
                        "errors" => array(),
                        "data" => null
                    );
                }

            } else {
                $response = array(
                    "status" => 404,
                    "status_text" => "error",
                    "api" => "matricula/api/matricula",
                    "method" => "PUT",
                    "message" => "curso no localizado",
                    "errors" => array(),
                    "data" => null
                );
            }
        } else {
            $response = array(
                "status" => 404,
                "status_text" => "error",
                "api" => "matricula/api/matricula",
                "method" => "PUT",
                "message" => "Identificador no localizado, La clave de curso o estudiante no fue enviada",
                "errors" => array(),
                "data" => null
            );
        }
        $this->response($response, 200);
    }

    // http://localhost/api_escuela/index.php/matricula/api/matricula/idcurso/4/idest/3
    public function matricula_delete()
    {
        if ($this->get('idcurso') && $this->get('idest')) {
            $curso_exists = $this->DAO->selectEntity('curso', array('curso_id' => $this->get('idcurso')), true);
            $estudiante_exists = $this->DAO->selectEntity('estudiante', array('estudiante_id' => $this->get('idest')), true);

            if ($curso_exists) {
                if ($estudiante_exists) {
                    $this->DAO->deleteItemEntity('matricula', array('estudiante_fk' => $this->get('idest'), 'curso_fk' => $this->get('idcurso')));
                    $response = array(
                        "status" => 200,
                        "status_text" => "succes",
                        "api" => "matricula/api/matricula",
                        "method" => "DELETE",
                        "message" => "Curso-disciplina borrado correctamente",
                        "data" => null
                    );
                } else {
                    $response = array(
                        "status" => 404,
                        "status_text" => "error",
                        "api" => "matricula/api/matricula",
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
                    "api" => "matricula/api/matricula",
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
                "api" => "matricula/api/matricula",
                "method" => "DELETE",
                "message" => "Identificador no localizado, La clave de curso o estudiante no fue enviada",
                "errors" => array(),
                "data" => null,
            );
        }
        $this->response($response, 200);
    }

}

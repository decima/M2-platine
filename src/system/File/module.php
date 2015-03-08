<?php

/**
 * @moduleName File
 *
 *
 * */
require_once("FileObject.php");

class File implements SystemModule {

    public function info() {
        return array(
            "name" => "File",
            "readablename" => "File"
        );
    }

    public function schema($schema = array()) {
        FileObject::schema($schema);
        return $schema;
    }

    public function menu($item = array()) {
        $item['/file/install'] = array(
            "access" => "access content",
            "callback" => array("File", "inst")
        );
        $item['/file/@'] = array(
            "access" => "access content",
            "callback" => array("File", "page_display_content")
        );
        $item['/file/@/download'] = array(
            "access" => "access content",
            "callback" => array("File", "page_download_content")
        );
        $item['/file/upload'] = array(
            "access" => "access content",
            "callback" => array("File", "page_upload_content")
        );
        return $item;
    }

    public static function page_download_content($id_file) {
        $theme = new Theme();
        $f = new FileObject();

        if($f->load($id_file)) {
            $f->nb_dl++;
            $f->save();

            $filename = Page::path($f->path);

            if(ini_get('zlib.output_compression')) {
                ini_set('zlib.output_compression', 'Off');
            }

            header('Pragma: public'); 	// required
            header("Content-Type: $f->content_type");
            header('Content-Disposition: attachment; filename="'.$f->id_file.'.'.$f->getExtension().'"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '.filesize($filename));	// provide file size
            header('Connection: close');
            readfile($filename);		// push it out
            exit();
        }
        else {
            $theme->process_404();
        }
        return;
    }

    public static function page_display_content($id_file) {
        $theme = new Theme();
        $f = new FileObject();

        if($f->load($id_file)) {
            header("Content-Type: $f->content_type");
            echo file_get_contents(Page::url($f->path));
        }
        else {
            $theme->process_404();
        }
        return;
    }

    public static function page_upload_content() {
        $theme = new Theme();

        if(isset($_FILES['file'])){
            $file = new FileObject();
            if($file -> uploadFile($_FILES['file'])){
                Notification::statusNotify(t("Le fichier a bien été uploadé."), Notification::STATUS_SUCCESS);
            } else {
                Notification::statusNotify(t("Une erreur s'est produite lors de l'upload."), Notification::STATUS_ERROR);
            }
        }

        $f = new Form("POST", Page::url("/file/upload"));
        $f -> setAttribute("enctype", "multipart/form-data");
        $t = (new InputElement("file", t("Fichier : "), "", "file"));
        $f->addElement($t);

        $t = (new InputElement("submit-file", "", t("Charger"), "submit"));
        $f->addElement($t);
        $formulaire = $theme->forming($f);
        $theme->set_title("Charger un fichier");
        $theme->add_to_body($formulaire);
        $theme->process_theme(Theme::STRUCT_ADMIN);
        return;
    }

    public static function inst(){
        ModuleManager::install_module("File", "./system/File/module.php");
    }


    public function priority() {
        return 100;
    }

    public function system_init() {
        // TODO: Implement system_init() method.
    }

}

<?php

/**
 * @name : News Module
 * @desc : News Module.
 * @mach : news
 * @author : d3cima
 * 
 */
class news implements m_install , m_access {

  

    public function perms() {
        
    }

    public function install() {
        content_database::create_new_content_type("News", "news");
    }

    public function uninstall() {
        content_database::delete_content_type("news");
    }

}

<?php

/**
 * @moduleName Doc
 *
 *
 * */
require_once('md.php');

class Doc implements Module
{

    public function info()
    {
        return [
            "name"         => "Doc",
            "readablename" => "Documentation Jinn",
        ];
    }

    public function menu($item = [])
    {

        $item['/doc/@'] = [
            "access"   => "access documentation",
            "callback" => ["Doc", "documentation"],
        ];
        $item['/doc/@/@'] = [
            "access"   => "access documentation",
            "callback" => ["Doc", "documentation"],
        ];
        return $item;
    }

    public static function titleWidget()
    {
        $svg = '<img style="width:150px;" src="' . Page::url("/modules/doc/logo.png") . '"/>';
        return "<div class='widget_titre titre title-doc'>" . $svg . Theme::linking(Page::url("/doc/getting-started"), t("Documentation"), false) . "</div>";
    }

    public function widget($items = [])
    {
        $items['documentation1'] = [
            "permissions" => "access documentation",
            "callback"    => ["Doc", "docWidget1"],
        ];
        $items['doc_title'] = [
            "permissions" => "access documentation",
            "callback"    => ["Doc", "titleWidget"],
        ];
        $items['documentation2'] = [
            "permissions" => "access documentation",
            "callback"    => ["Doc", "docWidget2"],
        ];
        return $items;
    }

    public static function docWidget1()
    {
        $t1 = t("<div class='widget_titre'><i class=\"fa fa-book\"></i> Utiliser Jinn</div>");
        $theme = new Theme();
        $list = [];

        $list[] = Theme::linking(Page::url("/doc/getting-started/install"), t("<i class=\"fa fa-dropbox fa-fw\"></i> Installer le système"));
        return $t1 . Theme::listing($list);
    }

    public static function docWidget2()
    {
        $t1 = t("<div class='widget_titre'><i class=\"fa fa-cubes\"></i> Modules</div>");
        $theme = new Theme();
        $list = [];


        $list[] = Theme::linking(Page::url("/doc/modules/create"), t("<i class=\"fa fa-cube fa-fw\"></i> Créer un module"));
        $list[] = Theme::linking(Page::url("/doc/modules/declared-hooks"), t("<i class=\"fa fa-anchor fa-fw\"></i> Hooks de référence"));


        return $t1 . Theme::listing($list);
    }

    public static function documentation($rubrique, $sousrubrique = 'index')
    {
        $r = "";
        $rubrique = str_replace(" ", "-", $rubrique);
        if (file_exists("modules/doc/docs/$rubrique/$sousrubrique.md")) {
            $file = file_get_contents("modules/doc/docs/$rubrique/$sousrubrique.md");
        } else {
            $file = file_get_contents("modules/doc/docs/notfound.md");
        }
        preg_match('/@title:(.*)/', $file, $matches);

        if (isset($matches[1])) {
            $file = str_replace("@title:" . $matches[1], "", $file);
            Theme::set_title($matches[1]);
        }

        $p = new Parsedown();
        $r = $p->text($file);
        $theme = new Theme();
        $theme->add_to_head("<link rel='stylesheet' href='" . Page::url("/modules/doc/styles/atelier-forest.dark.css") . "'/>");

        $theme->add_to_head("<script src='" . Page::url("/modules/doc/highlight.pack.js") . "'></script>");
        $theme->add_to_head("<script>hljs.initHighlightingOnLoad();</script>");

        $theme->add_to_body("<div style='margin:15px;'>$r</div>");
        $theme->process_theme(Theme::STRUCT_ADMIN);
    }

    public function permissions()
    {
        return true;
    }

}

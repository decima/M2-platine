<?php

function home() {

    $out = "there is actually no content. enable content module to manage this page";
    if (module_manager::is_enabled("content")) {

        $out = "";
        $nodes = content_database::node_load_all();
        $b = false;
        if (count($nodes) > 0) {
            page::title("Home");
            foreach ($nodes as $node) {

                if (content::node_access_read($node->nid)) {
                    $b = true;
                    $out .="<div class='post'>";
                    if ($node->title != null) {
                        $out .="<div class='title'>";
                        $out .=page::link("node/" . $node->nid, $node->title);
                        $out .="</div>";
                    }
                    if ($node->description != null) {
                        $out .="<div class='content'>";
                        $node->description = utf8_decode($node->description);


                        if (strlen($node->description) > 200) {

                            $out .=substr($node->description, 0, 200) . "... " .
                                    page::link("node/" . $node->nid, t("+ read more"));
                        }
                        else {

                            $out .=$node->description;
                        }
                        $out .="</div>";
                    }
                    $out .="<div class='author'>";
                    $out .="<hr/>";
                    $out .= content_page::post_author_date($node->uid, $node->author, $node->date);
                    $out .="<hr/>";

                    $out .="</div>";

                    $out .="</div>";
                }
            }
        }
        else {

            $out .="there is no content, please add a content first.";
        }
    }
    return $b ?
            $out : "there is no content.";
}

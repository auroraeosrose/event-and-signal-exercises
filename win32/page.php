<?php
// message to parse any argv/argc
define('PAGE_ARG_PARSE', 100);

// message for the page being created
define('PAGE_CREATE', 102);

// message for the page being rendered
define('PAGE_RENDER', 103);

// message for the page being displayed
define('PAGE_DISPLAY', 104);

// global list of all registered page types
$page_types = array();

// register a page type
function page_register_type($page_info) {

    foreach(array('message_proc', 'program', 'style', 'logo', 'layout', 'name') as $key) {
        if (!isset($page_info[$key])) {
            return false;
        }
    }

    if (isset($GLOBALS['page_types'][$page_info['name']])) {
        return false;
    }

    $GLOBALS['page_types'][$page_info['name']] = $page_info;

    return true;
}

// create a new page from a page type
function page_create($page_type, $title, $program) {

    if (!isset($GLOBALS['page_types'][$page_type])) {
        return false;
    }

    // we're using stdclass as a "struct" because page needs to be changable during the passing
    $page = $GLOBALS['page_types'][$page_type];
    $page = (object) $page;

    $page->title = $title;
    $page->program = $program;

    // stack some messages onto the queue
    message_post($page, PAGE_CREATE);

    return $page;
}

// do all the stuff for a page
function page_display($page) {
    message_post($page, PAGE_RENDER);
}

// default proc for all pages
function page_default_proc($page, $id, $data = null) {

    // we will switch on our message id
    switch($id) {
        case PAGE_CREATE:
            $data = message_send($page, PAGE_ARG_PARSE);
            if ($data) {
                $page->vars = array('argument' => $data);
            }
            break;
        case PAGE_RENDER:
            // variables for template?
            if (isset($page->vars)) {
                unset($id, $data);
                extract($page->vars);
            } else {
                unset($id, $data);
            }

            // include page
            ob_start();
            include $page->layout;
            $rendered = ob_get_clean();

            // post result to display
            message_post($page, PAGE_DISPLAY, $rendered);
            break;
        case PAGE_DISPLAY:
            echo $data;
            break;
    }

    return 0;
}
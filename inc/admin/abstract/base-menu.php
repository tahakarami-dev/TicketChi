<?php 

abstract class BASE_MENU{

protected $page_title;
protected $menu_title;
protected $capability;
protected $menu_slug;
protected $icon;
protected $has_sub_menu = false;
protected $sub_item;



public function __construct()
{
    $this->capability = 'manage_options';
    add_action('admin_menu', [$this, 'create_menu']);

}

public function create_menu(){

    add_menu_page(
        $this->page_title,
        $this->menu_title,
        $this->capability,
        $this->menu_slug,
        [$this , 'page'],
        $this->icon,


    );

    if($this->has_sub_menu){

        foreach($this->sub_item as $item){
              $hook =   add_submenu_page(
                    $this->menu_slug,
                    $item['page_title'],
                    $item['menu_title'],
                    $this->capability,
                    $item['menu_slug'],
                    [$this , $item['callback'],]
                     



        );

        if($item['load']['status']){

            add_action('load-' .$hook ,  [$this , $item['load']['callback_option'],]  );
        }


         

        }

    }

    remove_submenu_page( $this->menu_slug, $this->menu_slug);


}

    abstract public function page();
}
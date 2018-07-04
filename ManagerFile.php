<?php
require_once ('File.php');
require_once ('Folder.php');

class ManagerFile{
    private static $_instance = null;
    private $folders;
    private $files;

    private function __construct () {}
    private function __clone () {}
    private function __wakeup () {}

    public static function getInstance()
    {
        if (self::$_instance != null) {
            return self::$_instance;
        }

        return new self;
    }

    private function is_image($url)
    {
        $pos = strrpos($url, ".");
        if ($pos === false)
            return false;
        $ext = strtolower(trim(substr($url, $pos)));
        $imgExts = array(".gif", ".jpg", ".jpeg", ".png", ".tiff", ".tif"); // this is far from complete but that always going to be the case...
        if ( in_array($ext, $imgExts) )
            return true;
        return false;
    }

    private function is_text($url)
    {
        $pos = strrpos($url, ".");
        if ($pos === false)
            return false;
        $ext = strtolower(trim(substr($url, $pos)));
        $textExts = array(".txt", ".pdf"); // this is far from complete but that always going to be the case...
        if ( in_array($ext, $textExts) )
            return true;
        return false;
    }

    private function filesize_formatted($path)
    {
        $size = filesize($path);
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    public function managing(){
        $_GET["point"] = '.';

        //если не точки, то определяем её, что бы видеть, где мы находимся
        if(!isset($_GET["dir"])){
            $_GET["dir"] = $_GET["point"];
        }

        $this->folders = array();
        $this->files = array();

        //открываем каталог
        $od = opendir($_GET["dir"]);

        //читаем каталог
        while($file = readdir($od)){
            //проверяем если каталог и пропускаем точку и две точки
            if(is_dir($_GET["dir"]."/".$file) && $file!="." && $file!=".."){
                //создаем обьект и записываем его в массив
                $folder = new Folder();
                $folder->name = $file;
                $folder->size = $this->filesize_formatted($file);
                $folder->date = date ("F d Y H:i:s.", filemtime($file));
                $this->folders[] = $folder;
            }

            //проверяем если ли файл
            if(is_file($_GET["dir"]."/".$file)){
                //создаем обьект и записываем его в массив
                $file_item = new File();
                $file_item->name = $file;
                $file_item->size = $this->filesize_formatted($file);
                $file_item->date = date ("F d Y H:i:s.", filemtime($file));

                $this->files[] = $file_item;
            }
        }

        //закрываем каталог
        closedir($od);

        //вывод результата
        if($_GET["dir"] == '.')
        {
            print '<h2>Главная папка</h2>';
        }
        else{
            print '<h2>'.$_GET["dir"].'</h2>';
        }

        //стабильный переход на каталог вверх - начало
        if($_GET["dir"]!=$_GET["point"]){
            $poslslash=strrpos($_GET["dir"],"/");
            $newdir=substr($_GET["dir"],0,$poslslash);
            print "<a href='?dir=".$newdir."'>&lt;&lt;НАЗАД</a>";
        }
    }

    public function drawTableData(){
        foreach ($this->folders as $row) {
            echo '<tr>';
            foreach ($row as $item) {
                if(is_dir($item))
                {
                    echo "<td><a href='?dir=".$_GET["point"]."/".$item."'> {$item}</a></td>";
                }
                else
                {
                    echo "<td> {$item}</td>";
                }

            }
            echo '</tr>';
        }
        foreach ($this->files as $row) {
            echo '<tr>';
            foreach ((object)$row as $item) {
                if($this->is_image($item))
                {
                    echo "<td><a href='{$item}' alt='Image description' target='_blank'>{$item}</a></td>";
                }
                else if($this->is_text($item))
                {
                    echo "<td><a href='{$item}' target='_blank'>{$item}</a></td>";
                }
                else{
                    echo "<td> {$item}</td>";
                }
            }
            echo '</tr>';
        }
    }

    public function sortByName(){
        $isAsc = isset($_GET['sortname'])? (bool)$_GET['sortname']: 1;
        if($isAsc){
            function cmp_name($a, $b)
            {
                return strcmp($a->name, $b->name);
            }
            usort($this->folders, "cmp_name");
        }
        else{
            $this->folders = array_reverse($this->folders);
            $this->files = array_reverse($this->files);
        }
    }

//    public function sortByDate(){
//        $isAsc2 = isset($_GET['sortdate'])? (bool)$_GET['sortdate']: 1;
//        if($isAsc2){
//            function cmp_date($a, $b)
//            {
//                return strcmp($a->date, $b->date);
//            }
//            usort($this->folders, "cmp_date");
//            usort($this->files, "cmp_date");
//        }
//        else{
//            $this->folders = array_reverse($this->folders);
//            $this->files = array_reverse($this->files);
//        }
//    }
//
//    public function sortBySize(){
//        $isAsc = isset($_GET['sortsize'])? (bool)$_GET['sortsize']: 1;
//        if($isAsc){
//            function cmp_size($a, $b)
//            {
//                return strcmp($a->size, $b->size);
//            }
//            usort($this->folders, "cmp_size");
//            usort($this->files, "cmp_size");
//        }
//        else{
//            $this->folders = array_reverse($this->folders);
//            $this->files = array_reverse($this->files);
//        }
//    }
}
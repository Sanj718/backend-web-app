<?php
class Form{
    public static $error_result;
    public static function show_error($myerror){
        return  self::$error_result .= "<p>".$myerror."</p>";
    }
    public static function getAttr($attr){
        $res = "";
        foreach ($attr as $key=>$at){$res .= " $key='$at' ";}
        return $res;
    }
    public static function input($attr){
        return "<input ".self::getAttr($attr)." >";
    }
    public static function submit($attr){
        $attr['type'] = 'submit';
        return self::input($attr);

    }
    public static function select($attr, $options){
        $open = "<select ".self::getAttr($attr)." >";
        $close = "</select>";
        $optionsf = "";
        foreach ($options as $opt){
            $optionsf .= "<option ".strtolower($opt).">".strtoupper($opt)."</option>";
        }
        return $open.$optionsf.$close;
    }

    public static function textarea($attr){
        $val="";
        if(isset($attr['value'])){$val = $attr['value'];}
        return "<textarea  ".self::getAttr($attr).">".$val."</textarea>";
    }

}
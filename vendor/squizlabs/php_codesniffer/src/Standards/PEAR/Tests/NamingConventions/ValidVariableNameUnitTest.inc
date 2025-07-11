<?php
class MyClass
{
    var $varName  = 'hello';
    var $var_name = 'hello';
    var $varname  = 'hello';
    var $_varName = 'hello';

    public $varName  = 'hello';
    public $var_name = 'hello';
    public $varname  = 'hello';
    public $_varName = 'hello';

    protected $varName  = 'hello';
    protected $var_name = 'hello';
    protected $varname  = 'hello';
    protected $_varName = 'hello';

    private $_varName  = 'hello';
    private $_var_name = 'hello';
    private $_varname  = 'hello';
    private $varName   = 'hello';
}

class MyClass
{
  function func1()
  {
    function func2()
    {
     return $a;
    }
    return $data;
  }
}

class MyClass
{
    public function prepare() {}
    public function paint() {}
}

if (true) {
    class MyClass
    {
        var $varName  = 'hello';
        var $var_name = 'hello';
    }
}

class MyClass {
    function myFunction($cc, $cv) {
        $req = "delete from blah
                where not (POP_{$cc}_A =
'{$this->def["POP_{$cc}_A"]}'
                         and POP_{$cc}_B =
'{$this->def["POP_{$cc}_B"]}')";
    }
}

class mpgResponse{
   var $term_id;
   var $currentTag;
   function characterHandler($parser,$data){
       switch($this->currentTag)
       {
           case "term_id": {
               $this->term_id=$data;
               break;
           }
       }
   }//end characterHandler
}//end class mpgResponse

class foo
{
    const bar = <<<BAZ
qux
BAZ;
}

class foo
{
    var $c = <<<C
ccc
C;
}

class a
{
    protected
        $_sheet,
        $_FieldParser,
        $_key;
}

$util->setLogger(
    new class {
        private $varName  = 'hello';
        private $_varName = 'hello';
});

class AsymVisibility {
    // The read scope is public, but not specified. Enforce the naming conventions anyway.
    private(set) $asymPublicImplied  = 'hello';
    private(set) $_asymPublicImplied = 'hello';

    // The read scope is private, so these properties should be handled as private properties.
    private private(set) $asymPrivate  = 'hello';
    private(set) private $_asymPrivate = 'hello';

    // The read scope is public/protected, so these properties should be handled as public properties.
    public private(set) $asymPublicPrivate  = 'hello';
    private(set) protected $_asymPrivateProtected = 'hello';
}

# GeneratorTest Coding Standard

## Code Comparison, line length

Ensure there is no PHP &quot;Warning: str_repeat(): Second argument has to be greater than or equal to 0&quot;.  
Ref: squizlabs/PHP_CodeSniffer#2522
  <table>
   <tr>
    <th>Valid: contains line which is too long.</th>
    <th>Invalid: contains line which is too long.</th>
   </tr>
   <tr>
<td>

    class Foo extends Bar implements Countable, Serializable
    {
    }

</td>
<td>

    class Foo extends Bar
    {
        public static function foobar($param1, $param2) {}
    }

</td>
   </tr>
  </table>

Documentation generated on *REDACTED* by [PHP_CodeSniffer *VERSION*](https://github.com/PHPCSStandards/PHP_CodeSniffer)

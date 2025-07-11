# GeneratorTest Coding Standard

## Code Comparison, char encoding

This is a standard block.
  <table>
   <tr>
    <th>Valid: Vestibulum et orci condimentum.</th>
    <th>Invalid: Donec in nisl ut tortor convallis interdum.</th>
   </tr>
   <tr>
<td>

    <?php
    
    // The above PHP tag is specifically testing
    // handling of that in generated HTML doc.
    
    // Now let's also check the handling of
    // comparison operators in code samples...
    $a = $b < $c;
    $d = $e > $f;
    $g = $h <= $i;
    $j = $k >= $l;
    $m = $n <=> $o;

</td>
<td>

    <?php
    
    // The above PHP tag is specifically testing
    // handling of that in generated HTML doc.
    
    // Now let's also check the handling of
    // comparison operators in code samples
    // in combination with "em" tags.
    $a = $b < $c;
    $d = $e > $f;
    $g = $h <= $i;
    $j = $k >= $l;
    $m = $n <=> $o;

</td>
   </tr>
  </table>

Documentation generated on *REDACTED* by [PHP_CodeSniffer *VERSION*](https://github.com/PHPCSStandards/PHP_CodeSniffer)

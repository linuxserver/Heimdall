# GeneratorTest Coding Standard

## Code Comparison, block length

This is a standard block.
  <table>
   <tr>
    <th>Valid: code sample A has more lines than B.</th>
    <th>Invalid: shorter.</th>
   </tr>
   <tr>
<td>

    // This code sample has more lines
    // than the "invalid" one.
    $one = 10;

</td>
<td>

    $a = 10;

</td>
   </tr>
  </table>
  <table>
   <tr>
    <th>Valid: shorter.</th>
    <th>Invalid: code sample B has more lines than A.</th>
   </tr>
   <tr>
<td>

    echo $foo;

</td>
<td>

    // This code sample has more lines
    // than the "valid" one.
    print $foo;

</td>
   </tr>
  </table>

Documentation generated on *REDACTED* by [PHP_CodeSniffer *VERSION*](https://github.com/PHPCSStandards/PHP_CodeSniffer)

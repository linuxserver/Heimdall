# GeneratorTest Coding Standard

## Code Title, whitespace handling

This is a standard block.
  <table>
   <tr>
    <th>Valid: spaces at start of description.</th>
    <th>Invalid: spaces at end making line > 46 chars.</th>
   </tr>
   <tr>
<td>

    // Dummy.

</td>
<td>

    // Dummy.

</td>
   </tr>
  </table>
  <table>
   <tr>
    <th>Valid: spaces at start + end of description.</th>
    <th>Invalid: spaces '&nbsp;&nbsp;&nbsp;&nbsp; ' in description.</th>
   </tr>
   <tr>
<td>

    // Note: description above without the
    // trailing whitespace fits in 46 chars.

</td>
<td>

    // Dummy.

</td>
   </tr>
  </table>

Documentation generated on *REDACTED* by [PHP_CodeSniffer *VERSION*](https://github.com/PHPCSStandards/PHP_CodeSniffer)

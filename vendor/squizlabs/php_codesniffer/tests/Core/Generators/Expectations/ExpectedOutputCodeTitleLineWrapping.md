# GeneratorTest Coding Standard

## Code Title, line wrapping

This is a standard block.
  <table>
   <tr>
    <th>Valid: exactly 45 character long description.</th>
    <th>Invalid: exactly 45 char long description---.</th>
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
    <th>Valid: exactly 46 character long description-.</th>
    <th>Invalid: exactly 46 character long description</th>
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
    <th>Valid: exactly 47 character long description--.</th>
    <th>Invalid: exactly 47 character long description.</th>
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
    <th>Valid: this description is longer than 46 characters and will wrap.</th>
    <th>Invalid: this description is longer than 46 characters and will wrap.</th>
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

Documentation generated on *REDACTED* by [PHP_CodeSniffer *VERSION*](https://github.com/PHPCSStandards/PHP_CodeSniffer)

# GeneratorTest Coding Standard

## Code Comparison Only, Missing Standard Block

  <table>
   <tr>
    <th>Valid: Lorem ipsum dolor sit amet.</th>
    <th>Invalid: Maecenas non rutrum dolor.</th>
   </tr>
   <tr>
<td>

    class Code {}

</td>
<td>

    class Comparison {}

</td>
   </tr>
  </table>

## One Standard Block, Code Comparison

Documentation contains one standard block and one code comparison.
  <table>
   <tr>
    <th>Valid: Lorem ipsum dolor sit amet.</th>
    <th>Invalid: Maecenas non rutrum dolor.</th>
   </tr>
   <tr>
<td>

    class Code {}

</td>
<td>

    class Comparison {}

</td>
   </tr>
  </table>

## One Standard Block, No Code

Documentation contains one standard block and no code comparison.

## One Standard Block, Two Code Comparisons

Documentation contains one standard block and two code comparisons.
  <table>
   <tr>
    <th>Valid: Etiam commodo magna at vestibulum blandit.</th>
    <th>Invalid: Vivamus lacinia ante velit.</th>
   </tr>
   <tr>
<td>

    class Code {}

</td>
<td>

    class Comparison {}

</td>
   </tr>
  </table>
  <table>
   <tr>
    <th>Valid: Pellentesque nisi neque.</th>
    <th>Invalid: Mauris dictum metus quis maximus pharetra.</th>
   </tr>
   <tr>
<td>

    $one = 10;

</td>
<td>

    $a = 10;

</td>
   </tr>
  </table>

## Two Standard Blocks, No Code

This is standard block one.
This is standard block two.

## Two Standard Blocks, One Code Comparison

This is standard block one.
  <table>
   <tr>
    <th>Valid: Vestibulum et orci condimentum.</th>
    <th>Invalid: Donec in nisl ut tortor convallis interdum.</th>
   </tr>
   <tr>
<td>

    class Code {}

</td>
<td>

    class Comparison {}

</td>
   </tr>
  </table>
This is standard block two.

## Two Standard Blocks, Three Code Comparisons

This is standard block one.
  <table>
   <tr>
    <th>Valid: Vestibulum et orci condimentum.</th>
    <th>Invalid: Donec in nisl ut tortor convallis interdum.</th>
   </tr>
   <tr>
<td>

    class Code {}

</td>
<td>

    class Comparison {}

</td>
   </tr>
  </table>
This is standard block two.
  <table>
   <tr>
    <th>Valid: Pellentesque nisi neque.</th>
    <th>Invalid: Mauris dictum metus quis maximus pharetra.</th>
   </tr>
   <tr>
<td>

    $one = 10;

</td>
<td>

    $a = 10;

</td>
   </tr>
  </table>
  <table>
   <tr>
    <th>Valid: Quisque sagittis nisi vitae.</th>
    <th>Invalid: Morbi ac libero vitae lorem.</th>
   </tr>
   <tr>
<td>

    echo $foo;

</td>
<td>

    print $foo;

</td>
   </tr>
  </table>

Documentation generated on *REDACTED* by [PHP_CodeSniffer *VERSION*](https://github.com/PHPCSStandards/PHP_CodeSniffer)

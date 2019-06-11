<?php
namespace Lcobucci\JWT\FunctionalTests;

use Lcobucci\JWT\Signer\Ecdsa;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Ecdsa\Sha384;
use Lcobucci\JWT\Signer\Ecdsa\Sha512;
use Lcobucci\JWT\Signer\Key;
use PHPUnit\Framework\TestCase;
use const PHP_EOL;
use function assert;
use function hex2bin;
use function is_string;

final class RFC6978VectorTest extends TestCase
{
    /**
     * @see https://tools.ietf.org/html/rfc6979#appendix-A.2.5
     * @see https://tools.ietf.org/html/rfc6979#appendix-A.2.6
     * @see https://tools.ietf.org/html/rfc6979#appendix-A.2.7
     *
     * @test
     * @dataProvider dataRFC6979
     *
     * @covers \Lcobucci\JWT\Signer\Key
     * @covers \Lcobucci\JWT\Signer\Ecdsa
     * @covers \Lcobucci\JWT\Signer\Ecdsa\MultibyteStringConverter
     * @covers \Lcobucci\JWT\Signer\Ecdsa\Sha256
     * @covers \Lcobucci\JWT\Signer\Ecdsa\Sha384
     * @covers \Lcobucci\JWT\Signer\Ecdsa\Sha512
     * @covers \Lcobucci\JWT\Signer\OpenSSL
     * @covers \Lcobucci\JWT\Signer\BaseSigner
     */
    public function theVectorsFromRFC6978CanBeVerified(
        Ecdsa $signer,
        Key $key,
        $payload,
        $expectedR,
        $expectedS
    ) {
        $signature = hex2bin($expectedR . $expectedS);
        assert(is_string($signature));

        static::assertTrue($signer->verify($signature, $payload, $key));
    }

    /**
     * @return mixed[]
     */
    public function dataRFC6979()
    {
        return $this->sha256Data() + $this->sha384Data() + $this->sha512Data();
    }

    /**
     * @return mixed[]
     */
    public function sha256Data()
    {
        $signer = new Sha256();
        $key    = new Key(
            '-----BEGIN PUBLIC KEY-----' . PHP_EOL
            . 'MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEYP7UuiVanTHJYet0xjVtaMBJuJI7' . PHP_EOL
            . 'Yfps5mliLmDyn7Z5A/4QCLi8maQa6elWKLxk8vGyDC1+n1F3o8KU1EYimQ==' . PHP_EOL
            . '-----END PUBLIC KEY-----'
        );

        return [
            'SHA-256 (sample)' => [
                $signer,
                $key,
                'sample',
                'EFD48B2AACB6A8FD1140DD9CD45E81D69D2C877B56AAF991C34D0EA84EAF3716',
                'F7CB1C942D657C41D436C7A1B6E29F65F3E900DBB9AFF4064DC4AB2F843ACDA8',
            ],
            'SHA-256 (test)' => [
                $signer,
                $key,
                'test',
                'F1ABB023518351CD71D881567B1EA663ED3EFCF6C5132B354F28D3B0B7D38367',
                '019F4113742A2B14BD25926B49C649155F267E60D3814B4C0CC84250E46F0083',
            ]
        ];
    }

    /**
     * @return mixed[]
     */
    public function sha384Data()
    {
        $signer = new Sha384();
        $key    = new Key(
            '-----BEGIN PUBLIC KEY-----' . PHP_EOL
            . 'MHYwEAYHKoZIzj0CAQYFK4EEACIDYgAE7DpOQVtOGaRWhhgCn0J/pdqai8SukuAu' . PHP_EOL
            . 'BqrlKGswDGTe+PDqkFWGYGSiVFFUgLwTgBXZty19VyROqO+awMYhiWcIpZNn+d+5' . PHP_EOL
            . '9UyoSz8cnbEoiyMcOuDU/nNE/SUzJkcg' . PHP_EOL
            . '-----END PUBLIC KEY-----'
        );

        return [
            'SHA-384 (sample)' => [
                $signer,
                $key,
                'sample',
                '94EDBB92A5ECB8AAD4736E56C691916B3F88140666CE9FA73D64C4EA95AD133C81A648152E44ACF96E36DD1E80FABE46',
                '99EF4AEB15F178CEA1FE40DB2603138F130E740A19624526203B6351D0A3A94FA329C145786E679E7B82C71A38628AC8',
            ],
            'SHA-384 (test)' => [
                $signer,
                $key,
                'test',
                '8203B63D3C853E8D77227FB377BCF7B7B772E97892A80F36AB775D509D7A5FEB0542A7F0812998DA8F1DD3CA3CF023DB',
                'DDD0760448D42D8A43AF45AF836FCE4DE8BE06B485E9B61B827C2F13173923E06A739F040649A667BF3B828246BAA5A5',
            ]
        ];
    }

    /**
     * @return mixed[]
     */
    public function sha512Data()
    {
        $signer = new Sha512();
        $key    = new Key(
            '-----BEGIN PUBLIC KEY-----' . PHP_EOL
            . 'MIGbMBAGByqGSM49AgEGBSuBBAAjA4GGAAQBiUVQ0HhZMuAOqiO2lPIT+MMSH4bc' . PHP_EOL
            . 'l6BOWnFn205bzTcRI9RuRdtrXVNwp/IPtjMVXTj/oW0r12HcrEdLmi9QI6QASTEB' . PHP_EOL
            . 'yWLNTS/d94IoXmRYQTnC+RtH+H/4I1TWYw90aiig2yV0G1s0qCgAiyKswj+ST6r7' . PHP_EOL
            . '1NM/gepmlW3+qiv9/PU=' . PHP_EOL
            . '-----END PUBLIC KEY-----'
        );

        return [
            'SHA-512 (sample)' => [
                $signer,
                $key,
                'sample',
                '00C328FAFCBD79DD77850370C46325D987CB525569FB63C5D3BC53950E6D4C5F174E25A1EE9017B5D450606ADD152B534931D7D4E8'
                . '455CC91F9B15BF05EC36E377FA',
                '00617CCE7CF5064806C467F678D3B4080D6F1CC50AF26CA209417308281B68AF282623EAA63E5B5C0723D8B8C37FF0777B1A20F8CC'
                . 'B1DCCC43997F1EE0E44DA4A67A',
            ],
            'SHA-512 (test)' => [
                $signer,
                $key,
                'test',
                '013E99020ABF5CEE7525D16B69B229652AB6BDF2AFFCAEF38773B4B7D08725F10CDB93482FDCC54EDCEE91ECA4166B2A7C6265EF0C'
                . 'E2BD7051B7CEF945BABD47EE6D',
                '01FBD0013C674AA79CB39849527916CE301C66EA7CE8B80682786AD60F98F7E78A19CA69EFF5C57400E3B3A0AD66CE0978214D13BA'
                . 'F4E9AC60752F7B155E2DE4DCE3',
            ],
        ];
    }
}

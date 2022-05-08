<?php
/**
 * Copyright 2014, 2015 Brandon Black <blblack@gmail.com>
 *
 * This file is part of IPSet.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

namespace Wikimedia\IPSet\Test;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Wikimedia\IPSet;

/**
 * @group IPSet
 * @covers \Wikimedia\IPSet
 */
class IPSetTest extends TestCase {

	/**
	 * Provides test cases for IPSetTest::testIPSet
	 *
	 * Returns an array of test cases. Each case is an array of (description,
	 * config, tests).  Description is just text output for failure messages,
	 * config is an array constructor argument for IPSet, and the tests are
	 * an array of IP => expected (boolean) result against the config dataset.
	 */
	public static function provideIPSets() {
		$testcases = [
			'old_list_subset' => [
				[
					'208.80.152.162',
					'10.64.0.123',
					'10.64.0.124',
					'10.64.0.125',
					'10.64.0.126',
					'10.64.0.127',
					'10.64.0.128',
					'10.64.0.129',
					'10.64.32.104',
					'10.64.32.105',
					'10.64.32.106',
					'10.64.32.107',
					'91.198.174.45',
					'91.198.174.46',
					'91.198.174.47',
					'91.198.174.57',
					'2620:0:862:1:A6BA:DBFF:FE30:CFB3',
					'91.198.174.58',
					'2620:0:862:1:A6BA:DBFF:FE38:FFDA',
					'208.80.152.16',
					'208.80.152.17',
					'208.80.152.18',
					'208.80.152.19',
					'91.198.174.102',
					'91.198.174.103',
					'91.198.174.104',
					'91.198.174.105',
					'91.198.174.106',
					'91.198.174.107',
					'91.198.174.81',
					'2620:0:862:1:26B6:FDFF:FEF5:B2D4',
					'91.198.174.82',
					'2620:0:862:1:26B6:FDFF:FEF5:ABB4',
					'10.20.0.113',
					'2620:0:862:102:26B6:FDFF:FEF5:AD9C',
					'10.20.0.114',
					'2620:0:862:102:26B6:FDFF:FEF5:7C38',
				],
				[
					'0.0.0.0' => false,
					'255.255.255.255' => false,
					'10.64.0.122' => false,
					'10.64.0.123' => true,
					'10.64.0.124' => true,
					'10.64.0.129' => true,
					'10.64.0.130' => false,
					'91.198.174.81' => true,
					'91.198.174.80' => false,
					'0::0' => false,
					'ffff:ffff:ffff:ffff:FFFF:FFFF:FFFF:FFFF' => false,
					'2001:db8::1234' => false,
					'2620:0:862:1:26b6:fdff:fef5:abb3' => false,
					'2620:0:862:1:26b6:fdff:fef5:abb4' => true,
					'2620:0:862:1:26b6:fdff:fef5:abb5' => false,
				],
			],
			'new_cidr_set' => [
				[
					'208.80.154.0/26',
					'2620:0:861:1::/64',
					'208.80.154.128/26',
					'2620:0:861:2::/64',
					'208.80.154.64/26',
					'2620:0:861:3::/64',
					'208.80.155.96/27',
					'2620:0:861:4::/64',
					'10.64.0.0/22',
					'2620:0:861:101::/64',
					'10.64.16.0/22',
					'2620:0:861:102::/64',
					'10.64.32.0/22',
					'2620:0:861:103::/64',
					'10.64.48.0/22',
					'2620:0:861:107::/64',
					'91.198.174.0/25',
					'2620:0:862:1::/64',
					'10.20.0.0/24',
					'2620:0:862:102::/64',
					'10.128.0.0/24',
					'2620:0:863:101::/64',
					'10.2.4.26',
				],
				[
					'0.0.0.0' => false,
					'255.255.255.255' => false,
					'10.2.4.25' => false,
					'10.2.4.26' => true,
					'10.2.4.27' => false,
					'10.20.0.255' => true,
					'10.128.0.0' => true,
					'10.64.17.55' => true,
					'10.64.20.0' => false,
					'10.64.27.207' => false,
					'10.64.31.255' => false,
					'0::0' => false,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' => false,
					'2001:DB8::1' => false,
					'2620:0:861:106::45' => false,
					'2620:0:862:103::' => false,
					'2620:0:862:102:10:20:0:113' => true,
				],
			],
			'empty_set' => [
				[],
				[
					'0.0.0.0' => false,
					'255.255.255.255' => false,
					'10.2.4.25' => false,
					'10.2.4.26' => false,
					'10.2.4.27' => false,
					'10.20.0.255' => false,
					'10.128.0.0' => false,
					'10.64.17.55' => false,
					'10.64.20.0' => false,
					'10.64.27.207' => false,
					'10.64.31.255' => false,
					'0::0' => false,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' => false,
					'2001:DB8::1' => false,
					'2620:0:861:106::45' => false,
					'2620:0:862:103::' => false,
					'2620:0:862:102:10:20:0:113' => false,
				],
			],
			'edge_cases' => [
				[
					'0.0.0.0',
					'255.255.255.255',
					'::',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff',
					// host bits intentional
					'10.10.10.10/25',
				],
				[
					'0.0.0.0' => true,
					'255.255.255.255' => true,
					'10.2.4.25' => false,
					'10.2.4.26' => false,
					'10.2.4.27' => false,
					'10.20.0.255' => false,
					'10.128.0.0' => false,
					'10.64.17.55' => false,
					'10.64.20.0' => false,
					'10.64.27.207' => false,
					'10.64.31.255' => false,
					'0::0' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' => true,
					'2001:DB8::1' => false,
					'2620:0:861:106::45' => false,
					'2620:0:862:103::' => false,
					'2620:0:862:102:10:20:0:113' => false,
					'10.10.9.255' => false,
					'10.10.10.0' => true,
					'10.10.10.1' => true,
					'10.10.10.10' => true,
					'10.10.10.126' => true,
					'10.10.10.127' => true,
					'10.10.10.128' => false,
					'10.10.10.177' => false,
					'10.10.10.255' => false,
					'10.10.11.0' => false,
				],
			],
			'exercise_optimizer' => [
				[
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fffe:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fffd:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fffc:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fffb:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fffa:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff9:8000/113',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff9:0/113',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff8:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff7:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff6:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff5:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff4:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff3:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff2:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff1:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff0:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffef:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffee:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffec:0/111',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffeb:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffea:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe9:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe8:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe7:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe6:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe5:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe4:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe3:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe2:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe1:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe0:0/110',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffc0:0/107',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffa0:0/107',
					'ffff:ffff:ffff:ffff:ffff:ffff:fe00:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fe00:0/111',
				],
				[
					'0.0.0.0' => false,
					'255.255.255.255' => false,
					'::' => false,
					'ffff:ffff:ffff:ffff:ffff:ffff:ff9f:ffff' => false,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffa0:0' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffc0:1234' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffed:ffff' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:fff4:4444' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:fff9:8080' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:fe00:0' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:fe01:0' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:fe02:0' => false,
				],
			],
			'overlap' => [
				[
					// @covers addCidr "already added a larger supernet"
					'10.10.10.0/25',
					'10.10.10.0/26',
				],
				[
					'0.0.0.0' => false,
					'10.10.10.0' => true,
					'10.10.10.1' => true,
					'255.255.255.255' => false,
				],
			],
		];
		foreach ( $testcases as $desc => $pairs ) {
			$testcases[$desc] = [
				$desc,
				$pairs[0],
				$pairs[1],
			];
		}
		return $testcases;
	}

	/**
	 * Validates IPSet loading and matching code
	 *
	 * @dataProvider provideIPSets
	 */
	public function testIPSet( $desc, array $cfg, array $tests ) {
		$ipset = new IPSet( $cfg );
		foreach ( $tests as $ip => $expected ) {
			$result = $ipset->match( $ip );
			$this->assertEquals( $expected, $result, "Incorrect match() result for $ip in dataset $desc" );
		}
	}

	public static function provideBadMaskSets() {
		return [
			'bad mask ipv4' => [ '0.0.0.0/33' ],
			'bad mask ipv6' => [ '2620:0:861:1::/129' ],
		];
	}

	/**
	 * @dataProvider provideBadMaskSets
	 */
	public function testAddCidrWarning( $cidr ) {
		$this->expectWarning();
		$this->expectWarningMessageMatches( '/IPSet: Bad mask.*/' );

		// 1. Ignoring errors to reach the otherwise unreachable 'return'.
		// https://github.com/sebastianbergmann/php-code-coverage/issues/513
		// phpcs:ignore Generic.PHP.NoSilencedErrors
		@new IPSet( [ $cidr ] );

		// 2. Catches error as exception
		new IPSet( [ $cidr ] );
	}

	public static function provideBadIPSets() {
		return [
			'inet fail' => [ '0af.0af' ],
		];
	}

	/**
	 * @dataProvider provideBadIPSets
	 */
	public function testAddCidrFailure( $cidr ) {
		$method = new ReflectionMethod( IPSet::class, 'addCidr' );
		$method->setAccessible( true );
		$ipset = new IPSet( [ $cidr ] );
		$this->assertFalse( $method->invoke( $ipset, $cidr ) );
	}

	public static function provideBadMatches() {
		return [
			'inet fail' => [ '0af.0af', false ],
		];
	}

	/**
	 * @dataProvider provideBadMatches
	 */
	public function testMatchFailure( $ip, $expected ) {
		$ipset = new IPSet( [] );
		// phpcs:ignore Generic.PHP.NoSilencedErrors
		$this->assertEquals( $expected, @$ipset->match( $ip ) );
		$this->assertFalse( $ipset->match( $ip ) );
	}

	public function testSerialization() {
		$json = json_encode( new IPSet( [ '127.0.0.0/24' ] ) );

		$ipset = IPSet::newFromJson( $json );
		$this->assertTrue( $ipset->match( '127.0.0.1' ) );
		$this->assertFalse( $ipset->match( '10.0.0.1' ) );
	}
}

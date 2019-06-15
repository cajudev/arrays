<?php

use PHPUnit\Framework\TestCase;
use Cajudev\Arrays;

class Test {
    private   $private = 'lorem';
    public    $public = 'ipsum';
    protected $protected = 'dolor';
};

class ArraysTest extends TestCase
{
    public function test_construct_from_array()
    {
        $regularArray = ['lorem', 'ipsum', 'dolor', 'sit', 'amet,', 'consectetur', 'adipiscing', 'elit'];
        $arrays = new Arrays($regularArray);
        self::assertEquals($regularArray, $arrays->get());
    }

    public function test_construct_from_object_should_parse_attributes()
    {
        $object = new Test();
        $arrays = new Arrays($object);
        $expect = ['private' => 'lorem', 'public' => 'ipsum', 'protected' => 'dolor'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_construct_from_another_arrays_object()
    {
        $arrays1 = new Arrays([1, 2, 3]);
        $arrays2 = new Arrays($arrays1);
        self::assertSame([1, 2, 3], $arrays2->get());
    }

    public function test_construct_from_another_type_should_throws_exception()
    {
        self::expectException(\InvalidArgumentException::class);
        $arrays = new Arrays('lorem');
    }

    public function test_set_with_only_value()
    {
        $arrays = new Arrays();
        $arrays->set('lorem');
        self::assertSame(['lorem'], $arrays->get());
    }

    public function test_set_with_value_and_key()
    {
        $arrays = new Arrays();
        $arrays->set('lorem', 'ipsum');
        self::assertSame(['ipsum' => 'lorem'], $arrays->get());
    }

    public function test_set_with_more_then_one_key()
    {
        $arrays = new Arrays();
        $arrays->set('dolor', 'lorem', 'ipsum');
        self::assertSame(['lorem' => 'dolor', 'ipsum' => 'dolor'], $arrays->get());
    }

    public function test_set_using_dot_notation()
    {
        $arrays = new Arrays();
        $arrays->set('dolor', 'lorem.ipsum');
        self::assertSame(['lorem' => ['ipsum' => 'dolor']], $arrays->get());
    }

    public function test_length_after_set() {
        $arrays = new Arrays();
        $arrays->set('lorem');
        self::assertSame(1, $arrays->length);
    }

    public function test_push_one_value()
    {
        $arrays = new Arrays([1]);
        $arrays->push('lorem');
        self::assertEquals([1, 'lorem'], $arrays->get());
    }

    public function test_push_more_than_one_value()
    {
        $arrays = new Arrays([1]);
        $arrays->push('lorem', 'ipsum', 'dolor');
        self::assertEquals([1, 'lorem', 'ipsum', 'dolor'], $arrays->get());
    }

    public function test_length_after_push() {
        $arrays = new Arrays([1]);
        $arrays->push('lorem', 'ipsum', 'dolor');
        self::assertEquals(4, $arrays->length);
    }

    public function test_unshift_one_value()
    {
        $arrays = new Arrays([1]);
        $arrays->unshift('lorem');
        self::assertEquals(['lorem', 1], $arrays->get());
    }

    public function test_unshift_more_than_one_value()
    {
        $arrays = new Arrays([1]);
        $arrays->unshift('lorem', 'ipsum', 'dolor');
        self::assertEquals(['lorem', 'ipsum', 'dolor', 1], $arrays->get());
    }

    public function test_length_after_unshift() {
        $arrays = new Arrays([1]);
        $arrays->unshift('lorem', 'ipsum', 'dolor');
        self::assertEquals(4, $arrays->length);
    }

    public function test_shift_value_from_array()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor']);
        $expect = ['ipsum', 'dolor'];
        self::assertEquals($expect, $arrays->shift()->get());
    }

    public function test_length_after_shift() {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor']);
        $arrays->shift();
        self::assertEquals(2, $arrays->length);
    }

    public function test_pop_value_from_array()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor']);
        $arrays->pop();
        $expect = ['lorem', 'ipsum'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_length_after_pop() {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor']);
        $arrays->pop();
        self::assertEquals(2, $arrays->length);
    }

    public function test_get_a_value_from_array()
    {
        $arrays = new Arrays(['lorem' => 'ipsum']);
        self::assertEquals('ipsum', $arrays->get('lorem'));
    }

    public function test_get_should_return_instance_when_value_is_array()
    {
        $arrays = new Arrays(['lorem' => ['ipsum', 'dolor']]);
        self::assertInstanceOf(Arrays::class, $arrays->get('lorem'));
    }

    public function test_get_values_using_chained_get()
    {
        $arrays = new Arrays(['lorem' => ['ipsum' => 'dolor'], 'sit' => 'amet']);
        $expect = 'dolor';
        self::assertEquals($expect, $arrays->get('lorem')->get('ipsum'));
    }

    public function test_get_several_values()
    {
        $arrays = new Arrays(['lorem' => 'ipsum', 'dolor' => 'sit']);
        $expect = ['ipsum', 'sit', null];
        self::assertEquals($expect, $arrays->get('lorem', 'dolor', 'amet')->get());
    }

    public function test_get_values_using_dot_notation()
    {
        $arrays = new Arrays([
            'lorem' => [
                'ipsum' => [
                    'dolor' => 'sit'
                ]
            ]
        ]);
        $expect = 'sit';
        self::assertEquals($expect, $arrays->get('lorem.ipsum.dolor'));
    }

    public function test_get_inexistent_value_should_return_null()
    {
        $arrays = new Arrays(['lorem', 'ipsum']);
        self::assertEquals(null, $arrays->get('dolor'));
    }

    public function test_set_a_value_using_array_sintax()
    {
        $arrays = new Arrays();
        $arrays['lorem'] = 'ipsum';
        self::assertEquals(['lorem' => 'ipsum'], $arrays->get());
    }

    public function test_length_after_set_value_with_array_sintax() {
        $arrays = new Arrays();
        $arrays['lorem'] = 'ipsum';
        self::assertEquals(1, $arrays->length);
    }

    public function test_append_a_value_using_array_sintax()
    {
        $arrays = new Arrays();
        $arrays[] = 'ipsum';
        self::assertEquals(['ipsum'], $arrays->get());
    }

    public function test_length_after_append_with_array_sintax() {
        $arrays = new Arrays();
        $arrays[] = 'ipsum';
        self::assertEquals(1, $arrays->length);
    }

    public function test_accessing_invalid_keys_should_return_new_instance_with_empty_content()
    {
        $arrays = new Arrays();
        self::assertInstanceOf(Arrays::class, $arrays['ipsum']);
    }

    public function test_set_value_with_dot_notation() {
        $arrays = new Arrays();
        $arrays['lorem.ipsum.dolor'] = 'amet';
        $expect['lorem']['ipsum']['dolor'] = 'amet';
        self::assertEquals($expect, $arrays->get());
    }

    public function test_get_value_with_dot_notation() {
        $arrays = new Arrays();
        $arrays['lorem']['ipsum']['dolor'] = 'amet';
        self::assertEquals('amet', $arrays->get('lorem.ipsum.dolor'));
    }

    public function test_get_multiple_value_with_dot_notation() {
        $arrays = new Arrays();
        $arrays['lorem']['ipsum']['dolor'] = 'amet';
        $arrays['lorem']['ipsum']['sit'] = 'dolor';
        self::assertEquals(['amet', 'dolor'], $arrays->get('lorem.ipsum.dolor', 'lorem.ipsum.sit')->get());
    }

    public function test_interval_notation_index_key()
    {
        $arrays = new Arrays([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect = [2, 3, 4, 5, 6, 7];
        self::assertEquals($expect, $arrays->get('2:7')->get());
    }

    public function test_interval_notation_array_sintax_index_key()
    {
        $arrays = new Arrays([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect = [2, 3, 4, 5, 6, 7];
        self::assertEquals($expect, $arrays['2:7']->get());
    }

    public function test_interval_notation_reverse_index_key()
    {
        $arrays = new Arrays([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect = [7, 6, 5, 4, 3, 2];
        self::assertEquals($expect, $arrays->get('7:2')->get());
    }

    public function test_interval_notation_array_sintax_reverse_index_key()
    {
        $arrays = new Arrays([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $expect = [7, 6, 5, 4, 3, 2];
        self::assertEquals($expect, $arrays['7:2']->get());
    }

    public function test_length_after_interval_notation()
    {
        $arrays = new Arrays([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        self::assertEquals(5, $arrays->get('1:5')->length);
    }

    public function test_interval_notation_array_sintax_associative_key()
    {
        $arrays = new Arrays([
            'zero'  => 0, 'one'  => 1, 'two' => 2, 'three' => 3,
            'four'  => 4, 'five' => 5, 'six' => 6, 'seven' => 7,
            'eight' => 8, 'nine' => 9, 'ten' => 10
        ]);
        $expect = ['two'   => 2, 'three' => 3, 'four'  => 4, 'five'  => 5, 'six' => 6];
        self::assertEquals($expect, $arrays['2:6']->get());
    }

    public function test_interval_notation_array_sintax_reverse_associative_key()
    {
        $arrays = new Arrays([
            'zero'  => 0, 'one'  => 1, 'two' => 2, 'three' => 3,
            'four'  => 4, 'five' => 5, 'six' => 6, 'seven' => 7,
            'eight' => 8, 'nine' => 9, 'ten' => 10
        ]);
        $expect = ['six' => 6, 'five' => 5, 'four' => 4, 'three' => 3, 'two' => 2];
        self::assertSame($expect, $arrays['6:2']->get());
    }

    public function test_interval_notation_set_values()
    {
        $arrays = new Arrays();
        $arrays['5:8'] = 0;
        $expect = [5 => 0, 0, 0, 0];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_length_after_interval_notation_set_values()
    {
        $arrays = new Arrays();
        $arrays['5:8'] = 0;
        self::assertEquals(4, $arrays->length);
    }

    public function test_interval_notation_set_values_reverse()
    {
        $arrays = new Arrays();
        $arrays['8:5'] = 0;
        $expect = [5 => 0, 0, 0, 0];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_interval_notation_isset_should_return_false()
    {
        $arrays = new Arrays([1 => 'a', 'b', 'c', 'd', 'e']);
        self::assertFalse($arrays->_isset('1:6'));
    }

    public function test_interval_notation_isset_should_return_true()
    {
        $arrays = new Arrays([1 => 'a', 'b', 'c', 'd', 'e']);
        self::assertTrue($arrays->_isset('1:5'));
    }

    public function test_interval_notation_unset_values()
    {
        $arrays = new Arrays([0, 1, 2, 3, 4, 5]);
        $arrays->_unset('2:4');
        self::assertEquals([0, 1, 5 => 5], $arrays->get());
    }

    public function test_length_after_unset_with_interval_notation() {
        $arrays = new Arrays([0, 1, 2, 3, 4, 5]);
        $arrays->_unset('2:4');
        self::assertEquals(3, $arrays->length);
    }

    public function test_trying_access_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $arrays = new Arrays();
        $arrays['3^4'];
    }

    public function test_trying_set_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $arrays = new Arrays();
        $arrays['3^4'] = 10;
    }

    public function test_trying_isset_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $arrays = new Arrays();
        isset($arrays['3^4']);
    }

    public function test_trying_unset_using_wrong_pattern()
    {
        self::expectException(InvalidArgumentException::class);
        $arrays = new Arrays();
        unset($arrays['3^4']);
    }

    public function test_isset_should_return_true()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, isset($array['lorem']));
        self::assertEquals(true, $array->_isset('lorem'));
    }

    public function test_isset_should_return_false()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, isset($array['ipsum']));
        self::assertEquals(false, $array->_isset('ipsum'));
    }

    public function test_isset_with_dot_notation_should_return_true()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(true, isset($array['lorem.ipsum']));
        self::assertEquals(true, $array->_isset('lorem.ipsum'));
    }

    public function test_isset_with_dot_notation_should_return_false()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(false, isset($array['lorem.dolor']));
        self::assertEquals(false, $array->_isset('lorem.dolor'));
    }

    public function test_noset_should_return_false()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, $array->noset('lorem'));
    }

    public function test_noset_should_return_true()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, $array->noset('ipsum'));
    }

    public function test_noset_with_dot_notation_should_return_true()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(true, $array->noset('lorem.dolor'));
    }

    public function test_noset_with_dot_notation_should_return_false()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(false, $array->noset('lorem.ipsum'));
    }

    public function test_empty_should_return_false()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, $array->_empty('lorem'));
    }

    public function test_empty_should_return_true()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, $array->_empty('ipsum'));
    }

    public function test_empty_with_dot_notation_should_return_true()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = false;
        self::assertEquals(true, empty($array['lorem.ipsum']));
        self::assertEquals(true, $array->_empty('lorem.ipsum'));
    }

    public function test_empty_with_dot_notation_should_return_false()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'ipsum';
        self::assertEquals(false, empty($array['lorem.ipsum']));
        self::assertEquals(false, $array->_empty('lorem.ipsum'));
    }

    public function test_filled_should_return_true()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(true, $array->filled('lorem'));
    }

    public function test_filled_should_return_false()
    {
        $array = new Arrays();
        $array['lorem'] = 'ipsum';
        self::assertEquals(false, $array->filled('ipsum'));
    }

    public function test_filled_with_dot_notation_should_return_true()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'lorem';
        self::assertEquals(true, $array->filled('lorem.ipsum'));
    }

    public function test_filled_with_dot_notation_should_return_false()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = false;
        self::assertEquals(false, $array->filled('lorem.ipsum'));
    }

    public function test_unset_key_using_dot_notation()
    {
        $array = new Arrays();
        $array['lorem.ipsum'] = 'sit';
        $array->_unset('lorem.ipsum');
        self::assertEquals(false, $array->_isset('lorem.ipsum'));
    }

    public function test_iterating_array_foreach_should_return_regular_array()
    {
        $arrays = new Arrays([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        foreach ($arrays as $key => $value) {
            self::assertTrue(is_array($value));
        }
    }

    public function test_iterating_array_with_method_each()
    {
        $arrays = new Arrays([['lorem' => 'ipsum'], ['dolor' => 'sit']]);
        $arrays->each(function($key, $value) {
            self::assertTrue(is_array($value));
        });
    }

    public function test_iterating_using_method_for_forward()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor', 'sit']);
        $arrays->_for(0, 2, function($key, $value) use ($arrays) {
            self::assertEquals($arrays[$key], $value);
        });
    }

    public function test_iterating_using_method_for_backward()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor', 'sit']);
        $arrays->_for(3, -1, function($key, $value) use ($arrays) {
            self::assertEquals($arrays[$key], $value);
        });
    }

    public function test_map()
    {
        $arrays = new Arrays(['lorem', 'ipsum', 'dolor']);
        $arrays->map(function($key, $value) {
            return [$key + 10, strtoupper($value)];
        });
        $expect = [10 => 'LOREM', 11 => 'IPSUM', 12 => 'DOLOR'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_filter()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $arrays->filter(function($value, $key) {
            return $value < 5;
        });
        $expect = [1, 2, 3, 4];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_length_after_filter()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $arrays->filter(function($value, $key) {
            return $value < 5;
        });
        self::assertEquals(4, $arrays->length);
    }

    public function test_reduce()
    {
        $arrays = new Arrays([1, 2, 3, 4]);
        $result = $arrays->reduce(function($a, $b) {
            return $a + $b;
        });
        $expect = 10;
        self::assertEquals($expect, $result);
    }

    public function test_chunk()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5]);
        $expect = [0 => [1, 2], 1 => [3, 4], 2 => [5]];
        self::assertEquals($expect, $arrays->chunk(2)->get());
    }

    public function test_length_after_chunk()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5]);
        self::assertEquals(3, $arrays->chunk(2)->length);
    }

    public function test_keys()
    {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $keys = $arrays->keys();
        $expect = ['three', 'eight', 'two'];
        self::assertSame($expect, $keys->get());
    }

    public function test_values()
    {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $values = $arrays->values();
        $expect = [3, 8, 2];
        self::assertSame($expect, $values->get());
    }

    public function test_join()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5, 6]);
        $join   = $arrays->join('|');
        $expect = '1|2|3|4|5|6';
        self::assertEquals($expect, $join);
    }

    public function test_column()
    {
        $arrays = new Arrays([
            'lorem1' => [
                'ipsum' => 'dolor1',
                'sit'   => 'amet1'
            ],
            'lorem2' => [
                'ipsum' => 'dolor2',
                'sit'   => 'amet2'
            ]
        ]);
        $column   = $arrays->column('ipsum');
        $expect = ['dolor1', 'dolor2'];
        self::assertEquals($expect, $column->get());
    }

    public function test_combine()
    {
        $arrays = new Arrays();
        $arrays['KEYS']   = new Arrays(['lorem', 'ipsum']);
        $arrays['VALUES'] = new Arrays(['dolor', 'amet']);
        $arrays = Arrays::combine($arrays['KEYS'], $arrays['VALUES']);
        $expect = ['lorem' => 'dolor', 'ipsum'=> 'amet'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_count()
    {
        $arrays    = new Arrays([1, 2, 3, 4, 5]);
        $arrays[2] = [1, 2, 3];

        self::assertEquals(5, $arrays->count());
        self::assertEquals(3, $arrays[2]->count());
    }

    public function test_recursive_count()
    {
        $arrays = new Arrays([1, [2, 3], [2 => [1, 2, 3]]]);
        self::assertEquals(9, $arrays->count(COUNT_RECURSIVE));
    }

    public function test_recursive_count_should_not_affect_length()
    {
        $arrays = new Arrays([1, [2, 3], [2 => [1, 2, 3]]]);
        $arrays->count(COUNT_RECURSIVE);
        self::assertEquals(3, $arrays->length);
    }

    public function test_last()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5]);
        self::assertEquals(5, $arrays->last());
    }

    public function test_keyCase()
    {
        $arrays = new Arrays(['Hello' => 5]);
        
        $arrays->lower();
        self::assertEquals(['hello' => 5], $arrays->get());

        $arrays->upper();
        self::assertEquals(['HELLO' => 5], $arrays->get());
    }

    public function test_toString()
    {
        $arrays = new Arrays(['lorem' => 1, 'Ipsum' => 2]);
        $expect = '{"lorem":1,"Ipsum":2}';
        self::assertEquals($expect, $arrays);
    }

    public function test_sort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->sort();
        $expect = [2, 3, 8];
        self::assertSame($expect, $arrays->get());
    }

    public function test_rsort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->rsort();
        $expect = [8, 3, 2];
        self::assertSame($expect, $arrays->get());
    }

    public function test_asort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->asort();
        $expect = ['two' => 2, 'three' => 3, 'eight' => 8];
        self::assertSame($expect, $arrays->get());
    }

    public function test_arsort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->arsort();
        $expect = ['eight' => 8, 'three' => 3, 'two' => 2];
        self::assertSame($expect, $arrays->get());
    }

    public function test_ksort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->ksort();
        $expect = ['eight' => 8, 'three' => 3, 'two' => 2];
        self::assertSame($expect, $arrays->get());
    }

    public function test_krsort() {
        $arrays = new Arrays(['three' => 3, 'eight' => 8, 'two' => 2]);
        $arrays->krsort();
        $expect = ['two' => 2, 'three' => 3, 'eight' => 8];
        self::assertSame($expect, $arrays->get());
    }

    public function test_unique()
    {
        $arrays = new Arrays([1, 2, 3, 1, 2, 3, 1, 2, 3, 1, 2, 3]);
        $arrays->unique();
        $expect = [1, 2, 3];
        self::assertSame($expect, $arrays->get());
    }

    public function test_length_after_unique() {
        $arrays = new Arrays([1, 2, 3, 1, 2, 3, 1, 2, 3, 1, 2, 3]);
        $arrays->unique();
        self::assertEquals(3, $arrays->length);
    }

    public function test_merge()
    {
        $arrays = new Arrays([[1, 2], [2, 4], [4, 5, 13], [2, 3, 9]]);
        $arrays->merge();
        $expect = [1, 2, 2, 4, 4, 5, 13, 2, 3, 9];
        self::assertSame($expect, $arrays->get());
    }

    public function test_length_after_merge()
    {
        $arrays = new Arrays([[1, 2], [2, 4], [4, 5, 13], [2, 3, 9]]);
        $arrays->merge();
        self::assertEquals(10, $arrays->length);
    }

    public function test_reverse()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $arrays->reverse();
        $expect = [9, 8, 7, 6, 5, 4, 3, 2, 1];
        self::assertSame($expect, $arrays->get());
    }

    public function test_search()
    {
        $arrays = new Arrays(['a', 'b', 'c', 'd', 'e']);
        self::assertEquals(3, $arrays->search('d'));
    }

    public function test_union()
    {
        $arrays = new Arrays([[1, 2], [2, 4], [4, 5, 13], [2, 3, 9]]);
        $arrays->union();
        $expect = [1, 2, 4, 5, 13, 3, 9];
        self::assertSame($expect, $arrays->values()->get());
    }

    public function test_length_after_union()
    {
        $arrays = new Arrays([[1, 2], [2, 4], [4, 5, 13], [2, 3, 9]]);
        $arrays->union();
        self::assertEquals(7, $arrays->length);
    }

    public function test_diff()
    {
        $arrays = new Arrays([[1, 2, 3, 4, 5, 6], [2, 4], [6, 1]]);
        $arrays->diff();
        $expect = [3, 5];
        self::assertSame($expect, $arrays->values()->get());
    }

    public function test_length_after_diff()
    {
        $arrays = new Arrays([[1, 2, 3, 4, 5, 6], [2, 4], [6, 1]]);
        $arrays->diff();
        self::assertEquals(2, $arrays->length);
    }

    public function test_intersect()
    {
        $arrays = new Arrays([[1, 2, 3, 4, 5, 6], [2, 3, 5, 6], [2, 3, 7, 9]]);
        $arrays->intersect();
        $expect = [2, 3];
        self::assertSame($expect, $arrays->values()->get());
    }

    public function test_length_after_intersect()
    {
        $arrays = new Arrays([[1, 2, 3, 4, 5, 6], [2, 3, 5, 6], [2, 3, 7, 9]]);
        $arrays->intersect();
        self::assertEquals(2, $arrays->length);
    }

    public function test_cartesian()
    {
        $arrays = new Arrays([[1, 2], [3, 4], [3, 5]]);
        $arrays->cartesian();
        $expect = [[1, 3, 3], [1, 3, 5], [1, 4, 3], [1, 4, 5], [2, 3, 3], [2, 3, 5], [2, 4, 3], [2, 4, 5]];
        self::assertSame($expect, $arrays->get());
    }

    public function test_length_after_cartesian()
    {
        $arrays = new Arrays([[1, 2], [3, 4], [3, 5]]);
        $arrays->cartesian();
        self::assertEquals(8, $arrays->length);
    }

    public function test_backup_restore()
    {
        $arrays = new Arrays(['lorem' => 1, 'ipsum' => 2, 'dolor' => 3]);
        $arrays->backup();
        $arrays->_unset('lorem');
        $arrays->_unset('ipsum');
        $arrays->_unset('dolor');
        $arrays->restore();
        $expect = ['lorem' => 1, 'ipsum' => 2, 'dolor' => 3];
        self::assertSame($expect, $arrays->get());
    }

    public function test_flip()
    {
        $arrays = new Arrays(['lorem' => 1, 'ipsum' => 2, 'dolor' => 3]);
        $arrays->flip();
        $expect = [1 => 'lorem', 2 => 'ipsum', 3 => 'dolor'];
        self::assertSame($expect, $arrays->get());
    }

    public function test_sum()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        self::assertEquals(45, $arrays->sum());
    }

    public function test_contains_should_return_true()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        self::assertTrue($arrays->contains(7));
    }

    public function test_contains_should_return_false()
    {
        $arrays = new Arrays([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        self::assertFalse($arrays->contains(10));
    }

    public function test_length_after_override_value() {
        $arrays = new Arrays();
        $arrays['lorem'] = 'ipsum';
        $arrays['lorem'] = 'dolor';
        self::assertEquals(1, $arrays->length);
    }

    public function test_length_after_multidimensional_value() {
        $arrays = new Arrays();
        $arrays['lorem']['ipsum'] = 'dolor';
        $arrays['lorem']['amet'] = 'dolor';
        self::assertEquals(1, $arrays->length);
    }

    public function test_length_after_override_multidimensional_value() {
        $arrays = new Arrays();
        $arrays['lorem']['ipsum'] = 'dolor';
        $arrays['lorem']['ipsum'] = 'amet';
        self::assertEquals(1, $arrays->length);
    }

    public function test_length_after_add_value_dot_notation() {
        $arrays = new Arrays();
        $arrays['lorem.ipsum'] = 'dolor';
        self::assertEquals(1, $arrays->length);
    }

    public function test_length_after_override_value_dot_notation() {
        $arrays = new Arrays();
        $arrays['lorem.ipsum'] = 'dolor';
        $arrays['lorem.ipsum'] = 'amet';
        self::assertEquals(1, $arrays->length);
    }

    public function test_array_access_as_property() {
        $arrays = new Arrays();
        $arrays['lorem'] = 'dolor';
        self::assertEquals('dolor', $arrays->lorem);
    }

    public function test_array_set_value_as_property() {
        $arrays = new Arrays();
        $arrays->lorem = 'dolor';
        self::assertEquals(['lorem' => 'dolor'], $arrays->get());
    }

    public function test_array_check_isset_value_as_property() {
        $arrays = new Arrays();
        $arrays['lorem'] = 'dolor';
        self::assertTrue(isset($arrays->lorem));
        self::assertFalse(isset($arrays->ipsum));
    }

    public function test_array_unset_value_as_property() {
        $arrays = new Arrays();
        $arrays['lorem'] = 'dolor';
        unset($arrays->lorem);
        self::assertEquals([], $arrays->get());
    }
}
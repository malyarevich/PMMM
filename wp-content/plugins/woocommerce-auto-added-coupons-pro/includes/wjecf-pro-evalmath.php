<?php

/*
	//2.3.3-b3
	$math = new WJECF_EvalMath();
	$math->value_provider = new WJECF_Coupon_EvalValueBag( $coupon );
	//$formula = "echo(quantity_of_matching_products / 1 + 1)";
	$formula = "subtotal_of_matching_products";
	//try {
		$result = $math->evaluate( $formula );
		WJECF()->overwrite_value( $coupon, 'coupon_amount', $result );
		//echo "FORMULA $coupon->code : $formula = ". $result;
	//} 
	// catch (Exception $e) {
	// 	//echo "FORMULA $coupon->code : $formula FAILED: ". $e->getMessage();
	// 	$result = 0;
	// }
*/

/*

Changes compared to original:
- class names
- constructor
- removed arc / log stuff
- Rename: , $fb -> $functions, $f -> $userfunctions
- Removed $vb (constants)
- Moved functions to Valuebag
- value_provider instead of local variables/functions. 
*/


/*
================================================================================

EvalMath - PHP Class to safely evaluate math expressions
Copyright (C) 2005 Miles Kaufmann <http://www.twmagic.com/>

================================================================================

NAME
    EvalMath - safely evaluate math expressions
    
SYNOPSIS
    <?
      include('evalmath.class.php');
      $m = new EvalMath;
      // basic evaluation:
      $result = $m->evaluate('2+2');
      // supports: order of operation; parentheses; negation; built-in functions
      $result = $m->evaluate('-8(5/2)^2*(1-sqrt(4))-8');
      // create your own variables
      $m->evaluate('a = e^(ln(pi))');
      // or functions
      $m->evaluate('f(x,y) = x^2 + y^2 - 2x*y + 1');
      // and then use them
      $result = $m->evaluate('3*f(42,a)');
    ?>
      
DESCRIPTION
    Use the EvalMath class when you want to evaluate mathematical expressions 
    from untrusted sources.  You can define your own variables and functions,
    which are stored in the object.  Try it, it's fun!

METHODS
    $m->evalute($expr)
        Evaluates the expression and returns the result.  If an error occurs,
        prints a warning and returns false.  If $expr is a function assignment,
        returns true on success.
    
    $m->e($expr)
        A synonym for $m->evaluate().
    
    $m->vars()
        Returns an associative array of all user-defined variables and values.
        
    $m->funcs()
        Returns an array of all user-defined functions.

PARAMETERS
    $m->suppress_errors
        Set to true to turn off warnings when evaluating expressions

    $m->last_error
        If the last evaluation failed, contains a string describing the error.
        (Useful when suppress_errors is on).

AUTHOR INFORMATION
    Copyright 2005, Miles Kaufmann.

LICENSE
    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are
    met:
    
    1   Redistributions of source code must retain the above copyright
        notice, this list of conditions and the following disclaimer.
    2.  Redistributions in binary form must reproduce the above copyright
        notice, this list of conditions and the following disclaimer in the
        documentation and/or other materials provided with the distribution.
    3.  The name of the author may not be used to endorse or promote
        products derived from this software without specific prior written
        permission.
    
    THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
    IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
    INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
    (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
    SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
    HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
    STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
    ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
    POSSIBILITY OF SUCH DAMAGE.

*/

/**
 * Contains values for an expression.
 * When extending this class you can easily add getters/settters get_variablename() set_variablename( $value ).
 */
class WJECF_Eval_ValueProvider {
    private $variables = array();
    private $fc = array(); //function constraints (accepted number of arguments)

    public function __construct() {
    	//Number of required arguments
    	$this->fc = array(
    		'if' => array(3),
    		'limit' => array(3),
    		'mod' => array(2),
    		'round' => array(1,2),
    		'sqrt' => array(1),
    		'abs' => array(1),
    		'floor' => array(1),
    		'ceil' => array(1),
    	);
    }

    public function set( $name, $value ) {
        if ( $this->has_setter( $name ) ) {
            return call_user_func( $this->get_callable_setter( $name ), $value );
        }

        if ( $this->has_getter( $name ) ) {
            throw new Exception( WJECF_EvalMath::get_tr_string('cannotassigntoreadonly') );
        }
        return $this->variables[$name] = $value;
    }

    public function get( $name ) {
        if ( $this->has_getter( $name ) ) {
            return call_user_func( $this->get_callable_getter( $name ) );
        }
        return $this->variables[$name];
    }

    public function exists( $name ) {
        return array_key_exists($name, $this->variables) || $this->has_getter( $name );
    }

    //DEBUG
    public function vars() {
        $output = $this->variables;
        return $output;
    }    

// PRIVATE

    private function has_getter( $name ) {
        return is_callable( $this->get_callable_getter( $name ) );
    }

    private function get_callable_getter( $name ) {
        return array( $this, "get_" . $name );
    }

    private function has_setter( $name ) {
        return is_callable( $this->get_callable_setter( $name ) );
    }

    private function get_callable_setter( $name ) {
        return array( $this, "set_" . $name );
    } 


// FUNCTIONS any function starting with func_ can be called by the evaluator

    public function has_function( $name ) {
        return is_callable( array( $this, "func_" . $name ) );
    } 

    public function validate_args( $function_name, $arg_count ) {
    	if ( isset( $this->fc[$function_name] )) {
			$counts = $this->fc[$function_name];
			if (in_array(-1, $counts) and $arg_count > 0) {}
			elseif (!in_array($arg_count, $counts)) {
				$a= new stdClass();
				$a->function = $function_name;
				$a->expected = implode('/',$this->fc[$function_name]);
				$a->given = $arg_count;
				throw new Exception(WJECF_EvalMath::get_tr_string('wrongnumberofarguments', $a));
			} 
		}   	
    }  

    public function call_function( $name, $args ) {
        $res = call_user_func_array(array($this, 'func_' . $name), $args);
        return $res;
    } 

	public static function func_if( $condition, $then, $else ) {
		return ( (bool)$condition ? $then : $else );
	}

	public static function func_max() {
		$args = func_get_args();
		$res = array_pop($args);
		foreach($args as $a) {
			if ($res < $a) {
				$res = $a;
			}
		}
		return $res;
	}

	public static function func_min() {
		$args = func_get_args();
		$res = array_pop($args);
		foreach($args as $a) {
			if ($res > $a) {
				$res = $a;
			}
		}
		return $res;
	}

	public static function func_limit( $value, $min, $max ) {
		if ($value < $min)
			return $min;
		if ($value > $max)
			return $max;
		return $value;
	}

	public static function func_mod($op1, $op2) {
		return $op1 % $op2;
	}

	public static function func_round($val, $precision = 0) {
		return round($val, $precision);
	}

	public static function func_sum() {
		return array_sum( func_get_args() );
	}  


	public static function func_sqrt( $value ) {
		return sqrt( $value );
	}
	public static function func_abs( $value ) {
		return abs( $value );
	}
	public static function func_floor( $value ) {
		return floor( $value );
	}
	public static function func_ceil( $value ) {
		return ceil( $value );
	}


}



/*
================================================================================

EvalMath - PHP Class to safely evaluate math expressions
Copyright (C) 2005 Miles Kaufmann <http://www.twmagic.com/>

with modifications by Petr Skoda (skodak) from Moodle - http://moodle.org/
(this version: https://github.com/moodle/moodle/blob/4efc3d4096bc1d29e9d77f9af7194b2babfa2821/lib/evalmath/evalmath.class.php )

additional modifications by Tobias Bäthge:
- changed get_string() to MoodleTranslations::get_string(), which is a custom localization from Moodle
- allow comparisons (x>4, x=5, etc.)
- use array_sum() instead of loop in EvalMathFuncs::sum()
- add "product()" function
- add "mean()" alias for "average()"
- add "atan2()" and "arctan2()" alias
- add "median()", "mode()", and "range()" statistic functions
- add "if()" function
- add "number_format()" and "number_format_eu()" functions
- add "log10" function
- make "log" support the natural logarithm (with just one argument), and other bases (with second argument)
- Fix displaying of expected number of arguments

================================================================================

NAME
	EvalMath - safely evaluate math expressions

SYNOPSIS
	<?
	  include('evalmath.class.php');
	  $m = new EvalMath;
	  // basic evaluation:
	  $result = $m->evaluate('2+2');
	  // supports: order of operation; parentheses; negation; built-in functions
	  $result = $m->evaluate('-8(5/2)^2*(1-sqrt(4))-8');
	  // create your own variables
	  $m->evaluate('a = e^(ln(pi))');
	  // or functions
	  $m->evaluate('f(x,y) = x^2 + y^2 - 2x*y + 1');
	  // and then use them
	  $result = $m->evaluate('3*f(42,a)');
	?>

DESCRIPTION
	Use the EvalMath class when you want to evaluate mathematical expressions
	from untrusted sources.	 You can define your own variables and functions,
	which are stored in the object.	 Try it, it's fun!

METHODS
	$m->evalute($expr)
		Evaluates the expression and returns the result.  If an error occurs,
		prints a warning and returns false.	 If $expr is a function assignment,
		returns true on success.

	$m->e($expr)
		A synonym for $m->evaluate().

	$m->vars()
		Returns an associative array of all user-defined variables and values.

	$m->funcs()
		Returns an array of all user-defined functions.

PARAMETERS
	$m->suppress_errors
		Set to true to turn off warnings when evaluating expressions

	$m->last_error
		If the last evaluation failed, contains a string describing the error.
		(Useful when suppress_errors is on).

AUTHOR INFORMATION
	Copyright 2005, Miles Kaufmann.

LICENSE
	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are
	met:

	1	Redistributions of source code must retain the above copyright
		notice, this list of conditions and the following disclaimer.
	2.	Redistributions in binary form must reproduce the above copyright
		notice, this list of conditions and the following disclaimer in the
		documentation and/or other materials provided with the distribution.
	3.	The name of the author may not be used to endorse or promote
		products derived from this software without specific prior written
		permission.

	THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
	IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
	INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
	SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
	HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
	STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
	ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.

*/

class WJECF_EvalMath {

	/** @var string Pattern used for a valid function or variable name. Note, var and func names are case insensitive.*/
	private static $namepat = '[a-z][a-z0-9_]*';

	var $suppress_errors = false;
	var $last_error = null;

	var $value_provider = array(); // variables (and constants)
	var $userfunctions = array(); // User defined functions
	// var $fb = array(  // built-in functions
	// 	'sqrt','abs','ln','log10', 'exp','floor','ceil'
	// );

	var $allowimplicitmultiplication = false;

	function __construct() {
		$this->value_provider = new WJECF_Eval_ValueProvider();
		$this->allowimplicitmultiplication = false;
	}

	function e($expr) {
		return $this->evaluate($expr);
	}

	function evaluate($expr) {
		$this->last_error = null;
		$expr = trim($expr);
		if (substr($expr, -1, 1) == ';') $expr = substr($expr, 0, strlen($expr)-1); // strip semicolons at the end
		//===============
		// is it a variable assignment?
		if (preg_match('/^\s*('.self::$namepat.')\s*=\s*(.+)$/', $expr, $matches)) {
			if (in_array($matches[1], $this->vb)) { // make sure we're not assigning to a constant
				return $this->trigger(self::get_tr_string('cannotassigntoconstant', $matches[1]));
			}
			if (($tmp = $this->pfx($this->nfx($matches[2]))) === false) return false; // get the result and make sure it's good
			$this->value_provider->set( $matches[1] , $tmp ); // if so, stick it in the variable array
			return $this->value_provider->get( $matches[1] ); // and return the resulting value
		//===============
		// is it a function assignment?
		} elseif (preg_match('/^\s*('.self::$namepat.')\s*\(\s*('.self::$namepat.'(?:\s*,\s*'.self::$namepat.')*)\s*\)\s*=\s*(.+)$/', $expr, $matches)) {
			$fnn = $matches[1]; // get the function name
			// if (in_array($matches[1], $this->fb)) { // make sure it isn't built in
			// 	return $this->trigger(self::get_tr_string('cannotredefinebuiltinfunction', $matches[1]));
			// }
			$args = explode(",", preg_replace("/\s+/", "", $matches[2])); // get the arguments
			if (($stack = $this->nfx($matches[3])) === false) return false; // see if it can be converted to postfix
			for ($i = 0; $i<count($stack); $i++) { // freeze the state of the non-argument variables
				$token = $stack[$i];
				if (preg_match('/^'.self::$namepat.'$/', $token) and !in_array($token, $args)) {
					if ( $this->value_provider->exists( $token ) ) {
						$stack[$i] = $this->value_provider->get( $token );
					} else {
						return $this->trigger(self::get_tr_string('undefinedvariableinfunctiondefinition', $token));
					}
				}
			}
			$this->userfunctions[$fnn] = array('args'=>$args, 'func'=>$stack);
			return true;
		//===============
		} else {
			return $this->pfx($this->nfx($expr)); // straight up evaluation, woo
		}
	}

	function vars() {
		return $this->value_provider;
	}

	function funcs() {
		$output = array();
		foreach ($this->userfunctions as $fnn=>$dat)
			$output[] = $fnn . '(' . implode(',', $dat['args']) . ')';
		return $output;
	}

	/**
	 * @param string $name
	 * @return boolean Is this a valid var or function name?
	 */
	public static function is_valid_var_or_func_name($name){
		return preg_match('/'.self::$namepat.'$/iA', $name);
	}

	//===================== HERE BE INTERNAL METHODS ====================\\

	// Convert infix to postfix notation
	function nfx($expr) {

		$index = 0;
		$stack = new WJECF_EvalMathStack;
		$output = array(); // postfix form of expression, to be passed to pfx()
		$expr = trim(strtolower($expr));

		$ops   = array('+', '-', '*', '/', '^', '_', '>', '<', '=');
		$ops_r = array('+'=>0,'-'=>0,'*'=>0,'/'=>0,'^'=>1,'>'=>0,'<'=>0,'='=>0); // right-associative operator?
		$ops_p = array('+'=>0,'-'=>0,'*'=>1,'/'=>1,'_'=>1,'^'=>2,'>'=>0,'<'=>0,'='=>0); // operator precedence

		$expecting_op = false; // we use this in syntax-checking the expression
							   // and determining when a - is a negation

		if (preg_match("/[^\w\s+*^\/()\.,-<>=]/", $expr, $matches)) { // make sure the characters are all good
			return $this->trigger(self::get_tr_string('illegalcharactergeneral', $matches[0]));
		}

		while(1) { // 1 Infinite Loop ;)
			$op = substr($expr, $index, 1); // get the first character at the current index
			// find out if we're currently at the beginning of a number/variable/function/parenthesis/operand
			$ex = preg_match('/^('.self::$namepat.'\(?|\d+(?:\.\d*)?(?:(e[+-]?)\d*)?|\.\d+|\()/', substr($expr, $index), $match);
			//===============
			if ($op == '-' and !$expecting_op) { // is it a negation instead of a minus?
				$stack->push('_'); // put a negation on the stack
				$index++;
			} elseif ($op == '_') { // we have to explicitly deny this, because it's legal on the stack
				return $this->trigger(self::get_tr_string('illegalcharacterunderscore')); // but not in the input expression
			//===============
			} elseif ((in_array($op, $ops) or $ex) and $expecting_op) { // are we putting an operator on the stack?
				if ($ex) { // are we expecting an operator but have a number/variable/function/opening parethesis?
					if (!$this->allowimplicitmultiplication){
						return $this->trigger(self::get_tr_string('implicitmultiplicationnotallowed'));
					} else {// it's an implicit multiplication
						$op = '*';
						$index--;
					}
				}
				// heart of the algorithm:
				while($stack->count > 0 and ($o2 = $stack->last()) and in_array($o2, $ops) and ($ops_r[$op] ? $ops_p[$op] < $ops_p[$o2] : $ops_p[$op] <= $ops_p[$o2])) {
					$output[] = $stack->pop(); // pop stuff off the stack into the output
				}
				// many thanks: http://en.wikipedia.org/wiki/Reverse_Polish_notation#The_algorithm_in_detail
				$stack->push($op); // finally put OUR operator onto the stack
				$index++;
				$expecting_op = false;
			//===============
			} elseif ($op == ')' and $expecting_op) { // ready to close a parenthesis?
				while (($o2 = $stack->pop()) != '(') { // pop off the stack back to the last (
					if (is_null($o2)) return $this->trigger(self::get_tr_string('unexpectedclosingbracket'));
					else $output[] = $o2;
				}
				if (preg_match('/^('.self::$namepat.')\($/', $stack->last(2), $matches)) { // did we just close a function?
					$fnn = $matches[1]; // get the function name
					$arg_count = $stack->pop(); // see how many arguments there were (cleverly stored on the stack, thank you)
					$fn = $stack->pop();
					$output[] = array('fn'=>$fn, 'fnn'=>$fnn, 'argcount'=>$arg_count); // send function to output
					// if (in_array($fnn, $this->fb)) { // check the argument count
					// 	if($arg_count > 1) {
					// 		$a= new stdClass();
					// 		$a->function = $fnn;
					// 		$a->expected = 1;
					// 		$a->given = $arg_count;
					// 		return $this->trigger(self::get_tr_string('wrongnumberofarguments', $a));
					// 	}
					// } else
					if ( $this->value_provider->has_function( $fnn)) {
						$this->value_provider->validate_args( $fnn, $arg_count); //Throws exception on failure
					} elseif (array_key_exists($fnn, $this->userfunctions)) {
						if ($arg_count != count($this->userfunctions[$fnn]['args'])) {
							$a= new stdClass();
							$a->expected = count($this->userfunctions[$fnn]['args']);
							$a->given = $arg_count;
							return $this->trigger(self::get_tr_string('wrongnumberofarguments', $a));
						}
					} else { // did we somehow push a non-function on the stack? this should never happen
						return $this->trigger(self::get_tr_string('internalerror'));
					}
				}
				$index++;
			//===============
			} elseif ($op == ',' and $expecting_op) { // did we just finish a function argument?
				while (($o2 = $stack->pop()) != '(') {
					if (is_null($o2)) return $this->trigger(self::get_tr_string('unexpectedcomma')); // oops, never had a (
					else $output[] = $o2; // pop the argument expression stuff and push onto the output
				}
				// make sure there was a function
				if (!preg_match('/^('.self::$namepat.')\($/', $stack->last(2), $matches))
					return $this->trigger(self::get_tr_string('unexpectedcomma'));
				$stack->push($stack->pop()+1); // increment the argument count
				$stack->push('('); // put the ( back on, we'll need to pop back to it again
				$index++;
				$expecting_op = false;
			//===============
			} elseif ($op == '(' and !$expecting_op) {
				$stack->push('('); // that was easy
				$index++;
				$allow_neg = true;
			//===============
			} elseif ($ex and !$expecting_op) { // do we now have a function/variable/number?
				$expecting_op = true;
				$val = $match[1];
				if (preg_match('/^('.self::$namepat.')\($/', $val, $matches)) { // may be func, or variable w/ implicit multiplication against parentheses...
					if ( /* in_array($matches[1], $this->fb) or */ array_key_exists($matches[1], $this->userfunctions) or $this->value_provider->has_function( $matches[1]) ) { // it's a func
						$stack->push($val);
						$stack->push(1);
						$stack->push('(');
						$expecting_op = false;
					} else { // it's a var w/ implicit multiplication
						$val = $matches[1];
						$output[] = $val;
					}
				} else { // it's a plain old var or num
					$output[] = $val;
				}
				$index += strlen($val);
			//===============
			} elseif ($op == ')') {
				//it could be only custom function with no params or general error
				if ($stack->last() != '(' or $stack->last(2) != 1) return $this->trigger(self::get_tr_string('unexpectedclosingbracket'));
				if (preg_match('/^('.self::$namepat.')\($/', $stack->last(3), $matches)) { // did we just close a function?
					$stack->pop();// (
					$stack->pop();// 1
					$fn = $stack->pop();
					$fnn = $matches[1]; // get the function name

					$this->value_provider->validate_args( $fnn, 0); //Throws exception on failure

					// if ( isset( $this->fc[$fnn] ) )
					// 	$counts = $this->fc[$fnn]; // custom function
					// else
					// 	$counts = array(1); // default count for built-in functions
					// if (!in_array(0, $counts)){
					// 	$a= new stdClass();
					// 	$a->expected = $counts;
					// 	$a->given = 0;
					// 	$a->function = $fnn;
					// 	return $this->trigger(self::get_tr_string('wrongnumberofarguments', $a));
					// }
					$output[] = array('fn'=>$fn, 'fnn'=>$fnn, 'argcount'=>0); // send function to output
					$index++;
					$expecting_op = true;
				} else {
					return $this->trigger(self::get_tr_string('unexpectedclosingbracket'));
				}
			//===============
			} elseif (in_array($op, $ops) and !$expecting_op) { // miscellaneous error checking
				return $this->trigger(self::get_tr_string('unexpectedoperator', $op));
			} else { // I don't even want to know what you did to get here
				return $this->trigger(self::get_tr_string('anunexpectederroroccured'));
			}
			if ($index == strlen($expr)) {
				if (in_array($op, $ops)) { // did we end with an operator? bad.
					return $this->trigger(self::get_tr_string('operatorlacksoperand', $op));
				} else {
					break;
				}
			}
			while (substr($expr, $index, 1) == ' ') { // step the index past whitespace (pretty much turns whitespace
				$index++;							  // into implicit multiplication if no operator is there)
			}

		}
		while (!is_null($op = $stack->pop())) { // pop everything off the stack and push onto output
			if ($op == '(') return $this->trigger(self::get_tr_string('expectingaclosingbracket')); // if there are (s on the stack, ()s were unbalanced
			$output[] = $op;
		}
		return $output;
	}

	// evaluate postfix notation
	function pfx($tokens, $vars = array()) {

		if ($tokens == false) return false;

		$stack = new WJECF_EvalMathStack();

		foreach ($tokens as $token) { // nice and easy

			// if the token is a function, pop arguments off the stack, hand them to the function, and push the result back on
			if (is_array($token)) { // it's a function!
				$fnn = $token['fnn'];
				$count = $token['argcount'];
				// if (in_array($fnn, $this->fb)) { // built-in function:
				// 	if (is_null($op1 = $stack->pop())) return $this->trigger(self::get_tr_string('internalerror'));
				// 	$fnn = preg_replace("/^arc/", "a", $fnn); // for the 'arc' trig synonyms
				// 	if ($fnn == 'ln') $fnn = 'log'; // rewrite 'ln' (only allows one argument) to 'log' (natural logarithm)
				// 	eval('$stack->push(' . $fnn . '($op1));'); // perfectly safe eval()
				// } else
				if ( $this->value_provider->has_function( $fnn ) ) { // calc emulation function
					// get args
					$args = array();
					for ($i = $count-1; $i >= 0; $i--) {
						if (is_null($args[] = $stack->pop())) return $this->trigger(self::get_tr_string('internalerror'));
					}
					// if ($fnn == 'if') $fnn = 'func_if';
					// elseif ($fnn == 'mean') $fnn = 'average';
					// elseif ($fnn == 'arctan2') $fnn = 'atan2';
					$res = $this->value_provider->call_function( $fnn, array_reverse($args) );

//					$res = call_user_func_array(array($this->value_provider, 'func_' . $fnn), array_reverse($args));
					if ($res === FALSE) {
						return $this->trigger(self::get_tr_string('internalerror'));
					}
					$stack->push($res);
				} elseif (array_key_exists($fnn, $this->userfunctions)) { // user function
					// get args
					$args = array();
					for ($i = count($this->userfunctions[$fnn]['args'])-1; $i >= 0; $i--) {
						if (is_null($args[$this->userfunctions[$fnn]['args'][$i]] = $stack->pop())) return $this->trigger(self::get_tr_string('internalerror'));
					}
					$stack->push($this->pfx($this->userfunctions[$fnn]['func'], $args)); // yay... recursion!!!!
				}
			// if the token is a binary operator, pop two values off the stack, do the operation, and push the result back on
			} elseif (in_array($token, array('+', '-', '*', '/', '^', '>', '<', '='), true)) {
				if (is_null($op2 = $stack->pop())) return $this->trigger(self::get_tr_string('internalerror'));
				if (is_null($op1 = $stack->pop())) return $this->trigger(self::get_tr_string('internalerror'));
				switch ($token) {
					case '+':
						$stack->push($op1+$op2); break;
					case '-':
						$stack->push($op1-$op2); break;
					case '*':
						$stack->push($op1*$op2); break;
					case '/':
						if ($op2 == 0) return $this->trigger(self::get_tr_string('divisionbyzero'));
						$stack->push($op1/$op2); break;
					case '^':
						$stack->push(pow($op1, $op2)); break;
					case '>':
						$stack->push((int)($op1 > $op2)); break;
					case '<':
						$stack->push((int)($op1 < $op2)); break;
					case '=':
						$stack->push((int)($op1 == $op2)); break;
				}
			// if the token is a unary operator, pop one value off the stack, do the operation, and push it back on
			} elseif ($token == "_") {
				$stack->push(-1*$stack->pop());
			// if the token is a number or variable, push it on the stack
			} else {
				if (is_numeric($token)) {
					$stack->push($token);
				} elseif ( $this->value_provider->exists( $token ) ) {
					$stack->push($this->value_provider->get( $token ) );
				} elseif (array_key_exists($token, $vars)) {
					$stack->push($vars[$token]);
				} else {
					return $this->trigger(self::get_tr_string('undefinedvariable', $token));
				}
			}
		}
		// when we're out of tokens, the stack should have a single element, the final result
		if ($stack->count != 1) return $this->trigger(self::get_tr_string('internalerror'));
		return $stack->pop();
	}

	// trigger an error, but nicely, if need be
	function trigger($msg) {
		$this->last_error = $msg;
		if ( ! $this->suppress_errors ) throw new Exception( $msg );
		return false;
	}

	static function get_tr_string( $identifier, $a = NULL ) {
		// from https://github.com/moodle/moodle/blob/13264f35057d2f37374ec3e0e8ad4070f4676bd7/lang/en/mathslib.php
		$string = array();
		$string['anunexpectederroroccured'] = __( 'an unexpected error occured', 'woocommerce-jos-autocoupon' );
		$string['cannotassigntoconstant'] = __( 'cannot assign to constant \'{$a}\'', 'woocommerce-jos-autocoupon' );
		$string['cannotassigntoreadonly'] = __( 'cannot assign to readonly \'{$a}\'', 'woocommerce-jos-autocoupon' );
		$string['cannotredefinebuiltinfunction'] = __( 'cannot redefine built-in function \'{$a}()\'', 'woocommerce-jos-autocoupon' );
		$string['divisionbyzero'] = __( 'division by zero', 'woocommerce-jos-autocoupon' );
		$string['expectingaclosingbracket'] = __( 'expecting a closing bracket', 'woocommerce-jos-autocoupon' );
		$string['illegalcharactergeneral'] = __( 'illegal character \'{$a}\'', 'woocommerce-jos-autocoupon' );
		$string['illegalcharacterunderscore'] = __( 'illegal character \'_\'', 'woocommerce-jos-autocoupon' );
		$string['implicitmultiplicationnotallowed'] = __( 'expecting operator, implicit multiplication not allowed.', 'woocommerce-jos-autocoupon' );
		$string['internalerror'] = __( 'internal error', 'woocommerce-jos-autocoupon' );
		$string['operatorlacksoperand'] = __( 'operator \'{$a}\' lacks operand', 'woocommerce-jos-autocoupon' );
		$string['undefinedvariable'] = __( 'undefined variable \'{$a}\'', 'woocommerce-jos-autocoupon' );
		$string['undefinedvariableinfunctiondefinition'] = __( 'undefined variable \'{$a}\' in function definition', 'woocommerce-jos-autocoupon' );
		$string['unexpectedclosingbracket'] = __( 'unexpected closing bracket', 'woocommerce-jos-autocoupon' );
		$string['unexpectedcomma'] = __( 'unexpected comma', 'woocommerce-jos-autocoupon' );
		$string['unexpectedoperator'] = __( 'unexpected operator \'{$a}\'', 'woocommerce-jos-autocoupon' );
		$string['wrongnumberofarguments'] = __( 'wrong number of arguments for function \'{$a->function}()\' ({$a->given} given, {$a->expected} expected)', 'woocommerce-jos-autocoupon' );


		$string = $string[$identifier];

		// from https://github.com/moodle/moodle/blob/8e54ce9717c19f768b95f4332f70e3180ffafc46/lib/moodlelib.php#L6323
		if ($a !== NULL) {
			if (is_object($a) or is_array($a)) {
				$a = (array)$a;
				$search = array();
				$replace = array();
				foreach ($a as $key=>$value) {
					if (is_int($key)) {
						// we do not support numeric keys - sorry!
						continue;
					}
					if (is_object($value) or is_array($value)) {
						$value = (array)$value;
						if ( count( $value ) > 1 ) {
							$value = implode( ' or ', $value );
						} else {
							$value = (string)$value[0];
							if ( '-1' == $value )
								$value = 'at least 1';
						}
					}
					$search[]  = '{$a->'.$key.'}';
					$replace[] = (string)$value;
				}
				if ($search) {
					$string = str_replace($search, $replace, $string);
				}
			} else {
				$string = str_replace('{$a}', (string)$a, $string);
			}
		}

		return $string;
	}	

}

// for internal use
class WJECF_EvalMathStack {

	var $stack = array();
	var $count = 0;

	function push($val) {
		$this->stack[$this->count] = $val;
		$this->count++;
	}

	function pop() {
		if ($this->count > 0) {
			$this->count--;
			return $this->stack[$this->count];
		}
		return null;
	}

	function last($n=1) {
		if ($this->count - $n >= 0) {
			return $this->stack[$this->count-$n];
		}
		return null;
	}
}



class WJECF_Coupon_EvalValueBag extends WJECF_Eval_ValueProvider {

  /**
   * Value bag that knows some stuff about the coupon
   * @param WC_Coupon $coupon 
   * @return type
   */
  function __construct( $coupon ) {
    parent::__construct();
    $this->coupon = $coupon;
  }

//COUPON RESTRICTIONS (NOT ACTUALLY NECCESARY? BECAUSE THESE ARE VISIBLE TO THE ADMIN AND HE COULD COPY PASTE)

  function get_minimum_spend() {
    return $this->coupon->minimum_amount;
  }

  function get_maximum_spend() {
    return $this->coupon->maximum_amount;
  }  

  function get_minimum_quantity_of_matching_products() {
    return intval( get_post_meta( $this->coupon->id, '_wjecf_min_matching_product_qty', true ) );
  }

  function get_maximum_quantity_of_matching_products() {
    return intval( get_post_meta( $this->coupon->id, '_wjecf_max_matching_product_qty', true ) );
  }  

//VALUES THAT ALSO SHOULD CHECK THE CART

  function get_quantity_of_matching_products() {
    return WJECF()->get_quantity_of_matching_products( $this->coupon );
  }

  function get_subtotal_of_matching_products() {
    return WJECF()->get_subtotal_of_matching_products( $this->coupon );
  }  

  function get_quantity_of_products() {
    $qty = 0;
    foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		if ( ! isset( $cart_item['_wjecf_free_product_coupon'] ) ) { //Don't count free products
      		$qty += $cart_item['quantity'];
  		}
    }
    return $qty;
  }

}

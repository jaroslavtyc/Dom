<?php
namespace Gt\Dom;

class ParentNodeTest extends \PHPUnit_Framework_TestCase {

public function testChildren() {
	$document = new HTMLDocument(test\Helper::HTML_MORE);
	$children = $document->body->children;
	$this->assertNotSame($children, $document->body->childNodes);
	$this->assertNotCount($document->body->childNodes->length, $children);

	$firstImg = $document->querySelector("img");
	$this->assertSame($firstImg, $children->item(1));
}

public function testFirstLastElementChild() {
	$document = new HTMLDocument(test\Helper::HTML_MORE);
	$this->assertInstanceOf(
		"\Gt\Dom\Text", $document->body->firstChild);
	$this->assertInstanceOf(
		"\Gt\Dom\Element", $document->body->firstElementChild);
}

public function testChildElementCount() {
	$document = new HTMLDocument(test\Helper::HTML_MORE);
	$this->assertInstanceOf(
		"\Gt\Dom\Text", $document->body->lastChild);
	$this->assertInstanceOf(
		"\Gt\Dom\Element", $document->body->lastElementChild);
}

public function testContains() {
	$document = new HTMLDocument(test\Helper::HTML_MORE);
	$field = $document->querySelector("input[name=fieldA]");
	$form2 = $document->forms[1];
// $field is contained within the body, but not the form2 node.
	$this->assertTrue($document->body->contains($field));
	$this->assertFalse($form2->contains($field));
}

public function testIsSameNode() {
	$document = new HTMLDocument(test\Helper::HTML_MORE);
	$input = $document->querySelector("input");
	$this->assertTrue(
		$input->isSameNode(
			$document->querySelector("[name=fieldA]")
		)
	);
	$this->assertFalse(
		$input->isSameNode(
			$document->querySelector("[name=fieldB]")
		)
	);
}

}#
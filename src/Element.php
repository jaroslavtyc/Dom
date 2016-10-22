<?php
namespace phpgt\dom;

use DOMXPath;
use Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * Represents an object of a Document.
 */
class Element extends \DOMElement {
use LiveProperty, NonDocumentTypeChildNode, ChildNode, ParentNode;

private $classList;

/**
 * Returns the first element within the document (using depth-first pre-order
 * traversal of the document's nodes|by first element in document markup and
 * iterating through sequential nodes by order of amount of child nodes) that
 * matches the specified group of selectors.
 *
 * @param string $selectors One or more CSS selectors separated by commas
 * @return Element|null Returns null if no matches are found; otherwise, it
 * returns the first matching element.
 */
public function querySelector(string $selectors) {
	$htmlCollection = $this->css($selectors);
	return $htmlCollection->item(0);
}

/**
 * Returns a list of the elements within the document (using depth-first
 * pre-order traversal of the document's nodes) that match the specified group
 * of selectors.
 *
 * @param string $selectors One or more CSS selectors separated by commas
 * @return HTMLCollection Contains all the elements in the document that are
 * matched by any of the specified selectors.
 */
public function querySelectorAll(string $selectors):HTMLCollection {
	return $this->css($selectors);
}

/**
 * returns true if the element would be selected by the specified selector
 * string; otherwise, returns false.
 *
 * @param string $selectors One or more CSS selectors separated by commas
 * @return bool True if this element is selectable by provided selector
 */
public function matches(string $selectors):bool {
	$matches = $this->ownerDocument->querySelectorAll($selectors);
	$i = $matches->length;
	while(--$i >= 0 && $matches->item($i) !== $this);

	return($i >= 0);
}

/**
 * Returns a live HTMLCollection containing all child elements which have all
 * of the given class names. When called on the document object, the complete
 * document is searched, including the root node.
 *
 * @param string $names a string representing the list of class names to
 *  match; class names are separated by whitespace
 * @return HTMLCollection Contains all the elements in the document that are
 * matched by any of the specified class names.
 */
public function getElementsByClassName(string $names):HTMLCollection {
	$namesArray = explode(" ", $names);
	$dots = "." . implode(".", $namesArray);
	return $this->css($dots);
}
/**
 * Returns the closest ancestor of the current element (or itself)
 * which matches the selectors.
 * @param string $selectors css selectors
 * @return Element|null Returns null if no matches are found; otherwise, it
 * returns the first matching element.
 */
public function closest(string $selectors) {
	$collection = $this->css($selectors, "ancestor-or-self::");
	return $collection->item(count($collection) - 1);
}

/**
 * Internal function used to convert a provided CSS selector into a XPath query,
 * usable by the native DOMXPath class.
 *
 * @param string $selector One or more CSS selectors separated by commas
 * @param string $prefix Optional. XPath query to prefix with (default matches
 * the CSS selector mechanism)
 * @return HTMLCollection Contains all the elements in the document that are
 * matched by any of the specified selectors.
 */
private function css(
string $selector, string $prefix = "descendant-or-self::"):HTMLCollection {
	$converter = new CssSelectorConverter();
	$xPathSelector = $converter->toXPath($selector, $prefix);
	return $this->xPath($xPathSelector);
}

/**
 * Performs an XPath query on the current Element, returning an HTMLCollection
 * of matching Elements.
 *
 * @param string $queries One or more XPath queries, separated by commas
 * @return HTMLCollection Contains all the elements in the document that are
 * matched by any of the specified queries.
 */
private function xPath(string $queries):HTMLCollection {
	$x = new DOMXPath($this->ownerDocument);
	return new HTMLCollection($x->query($queries, $this));
}

public function prop_get_classList() {
	if(!$this->classList) {
		$this->classList = new TokenList($this, "class");
	}

	return $this->classList;
}

public function prop_get_value() {
	$methodName = 'value_get_' . $this->tagName;
	if(method_exists($this, $methodName)) {
		return $this->$methodName();
	}

	return null;
}

public function prop_set_value($newValue) {
	$methodName = 'value_set_' . $this->tagName;
	if(method_exists($this, $methodName)) {
		return $this->$methodName($newValue);
	}
}

private function value_set_select($newValue) {
	$options = $this->getElementsByTagName('option');
	$selectedIndexes = [];
	$newSelectedIndex = NULL;

	for($i = $options->length - 1; $i >= 0; --$i) {
		if(self::isSelectOptionSelected($options->item($i))) {
			$selectedIndexes[] = $i;
		}

		if($options->item($i)->getAttribute('value') == $newValue) {
			$newSelectedIndex = $i;
		}
	}

	if($newSelectedIndex !== NULL) {
		foreach ($selectedIndexes as $i) {
			$options->item($i)->removeAttribute('selected');
		}

		$options->item($newSelectedIndex)->setAttribute('selected', 'selected');
	}
}

private function value_get_select() {
	$options = $this->getElementsByTagName('option');
	if ($options->length == 0) {
		$value = '';
	}
	else {
		$value = $options->item(0)->getAttribute('value');
	}

	foreach ($options as $option) {
		if (self::isSelectOptionSelected($option)) {
			$value = $option->getAttribute('value');
			break;
		}
	}

	return $value;
}

static public function isSelectOptionSelected(Element $option) {
	return $option->hasAttribute('selected') && $option->getAttribute('selected');
}

}#
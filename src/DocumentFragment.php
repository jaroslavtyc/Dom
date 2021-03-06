<?php
namespace Gt\Dom;

/**
 * Represents a minimal document object that has no parent. It is used as a
 * light-weight version of Document to store well-formed or potentially
 * non-well-formed fragments of XML.
 *
 * Various other methods can take a document fragment as an argument (e.g.,
 * any Node interface methods such as Node.appendChild and Node.insertBefore),
 * in which case the children of the fragment are appended or inserted, not
 * the fragment itself.
 *
 * This interface is also of great use with Web components: <template>
 * elements contains a DocumentFragment in their HTMLTemplateElement::$content
 * property.
 *
 * An empty DocumentFragment can be created using the
 * Document::createDocumentFragment() method or the constructor.
 */
class DocumentFragment extends \DOMDocumentFragment {
	use LiveProperty, ParentNode;

	protected function getRootDocument():\DOMDocument {
		return $this->ownerDocument;
	}
}

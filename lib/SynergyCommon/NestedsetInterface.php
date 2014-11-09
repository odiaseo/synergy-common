<?php
namespace SynergyCommon;

/**
 * Interface to allow the management of nested set tree classess
 *
 * Class NestedsetInterface
 *
 *
 *
 * @package SynergyCommon
 */
interface NestedsetInterface
{
    /**
     * Creates a new node as child of a parent node or as a root node
     *
     * @param       $parentId
     * @param array $data
     *
     * @return mixed
     */
    public function createNode($parentId, $data = array());

    /**
     * Move a node relatived to the reference node in the direction specified
     *
     * Direction can be: after, before, last or first
     *
     * @param $node
     * @param $referenceNode
     * @param $direction
     *
     * @return mixed
     */
    public function moveNode($node, $referenceNode, $direction);

    /**
     * Delete a node from the nested set tree
     *
     * @param $node
     *
     * @return mixed
     */
    public function removeNode($node);
}
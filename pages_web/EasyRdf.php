<?php

/**
 * lib
 *
 * Use this file to load the core of lib, if you don't have an autoloader.
 *
 *
 * LICENSE
 *
 * Copyright (c) 2009-2013 Nicholas J Humfrey.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. The name of the author 'Nicholas J Humfrey" may be used to endorse or
 *    promote products derived from this software without specific prior
 *    written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    lib
 * @copyright  Copyright (c) 2011-2013 Nicholas J Humfrey
 * @license    http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * @see lib_Exception
 */
require_once "lib/Exception.php";

/**
 * @see lib_Format
 */
require_once "lib/Format.php";

/**
 * @see lib_Graph
 */
require_once "lib/Graph.php";

/**
 * @see lib_GraphStore
 */
require_once "lib/GraphStore.php";

/**
 * @see lib_Http
 */
require_once "lib/Http.php";

/**
 * @see lib_Http_Client
 */
require_once "lib/Http/Client.php";

/**
 * @see lib_Http_Response
 */
require_once "lib/Http/Response.php";

/**
 * @see lib_Isomorphic
 */
require_once "lib/Isomorphic.php";

/**
 * @see lib_Namespace
 */
require_once "lib/RdfNamespace.php";

/**
 * @see lib_Literal
 */
require_once "lib/Literal.php";

/**
 * @see lib_Literal_Boolean
 */
require_once "lib/Literal/Boolean.php";

/**
 * @see lib_Literal_Date
 */
require_once "lib/Literal/Date.php";

/**
 * @see lib_Literal_DateTime
 */
require_once "lib/Literal/DateTime.php";

/**
 * @see lib_Literal_Decimal
 */
require_once "lib/Literal/Decimal.php";

/**
 * @see lib_Literal_HexBinary
 */
require_once "lib/Literal/HexBinary.php";

/**
 * @see lib_Literal_HTML
 */
require_once "lib/Literal/HTML.php";

/**
 * @see lib_Literal_Integer
 */
require_once "lib/Literal/Integer.php";

/**
 * @see lib_Literal_XML
 */
require_once "lib/Literal/XML.php";

/**
 * @see lib_ParsedUri
 */
require_once "lib/ParsedUri.php";

/**
 * @see lib_Parser
 */
require_once "lib/Parser.php";

/**
 * @see lib_Parser_Exception
 */
require_once "lib/Parser/Exception.php";

/**
 * @see lib_Parser_RdfPhp
 */
require_once "lib/Parser/RdfPhp.php";

/**
 * @see lib_Parser_Ntriples
 */
require_once "lib/Parser/Ntriples.php";

/**
 * @see lib_Parser_Json
 */
require_once "lib/Parser/Json.php";

/**
 * @see lib_Parser_Rdfa
 */
require_once "lib/Parser/Rdfa.php";

/**
 * @see lib_Parser_RdfXml
 */
require_once "lib/Parser/RdfXml.php";

/**
 * @see lib_Parser_Turtle
 */
require_once "lib/Parser/Turtle.php";

/**
 * @see lib_Resource
 */
require_once "lib/Resource.php";

/**
 * @see lib_Collection
 */
require_once "lib/Collection.php";

/**
 * @see lib_Container
 */
require_once "lib/Container.php";

/**
 * @see lib_Serialiser
 */
require_once "lib/Serialiser.php";

/**
 * @see lib_Serialiser_GraphViz
 */
require_once "lib/Serialiser/GraphViz.php";

/**
 * @see lib_Serialiser_RdfPhp
 */
require_once "lib/Serialiser/RdfPhp.php";

/**
 * @see lib_Serialiser_Ntriples
 */
require_once "lib/Serialiser/Ntriples.php";

/**
 * @see lib_Serialiser_Json
 */
require_once "lib/Serialiser/Json.php";

/**
 * @see lib_Serialiser_RdfXml
 */
require_once "lib/Serialiser/RdfXml.php";

/**
 * @see lib_Serialiser_Turtle
 */
require_once "lib/Serialiser/Turtle.php";

/**
 * @see lib_Sparql_Client
 */
require_once "lib/Sparql/Client.php";

/**
 * @see lib_Sparql_Result
 */
require_once "lib/Sparql/Result.php";

/**
 * @see lib_TypeMapper
 */
require_once "lib/TypeMapper.php";

/**
 * @see lib_Utils
 */
require_once "lib/Utils.php";

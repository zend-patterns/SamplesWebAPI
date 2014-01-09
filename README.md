WebAPI Samples application
==========================

Zend Server's extensive Web API covers all of the available functionality Zend Server provides. By connecting to the Web API you can reach and connect to the services on Zend Server and integrate it into applications and external services to automate your processes.

This package of samples provides you with at least a working understanding of how the Web API works and how we thought you would connect to and use it.

The samples break down into three sections:
* Basics - explanations on how to actually connect to the Web API.
* Simple workflows - some easy to use Web API actions to get your started.
* Advanced workflows - Web API actions which require more than one step to actually be used effectively.

The samples package also contains information about another Zend project - Zend Server Web API Module. This module can be dropped into any ZF2 application and be used to make integration even easier.

Installation
============
This repository contains a deployment.xml file necessary to create a zpk file for deployment using Zend Server's Deployment feature. It can also just be copy-pasted into an existing directory on a webhost but was not tested for this scenario.

Note that this application requires Zend Server as it relies on the ZF2 library package and the Deployment feature to locate it.

Examples
========
The example scripts included in this package will actually connect to your server and retrieve information from it. We purposefully avoided storing any credentials as part of the implementation of this package and you will have to supply a Web API key and name pair for any example script you use.

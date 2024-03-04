

# API information exposed by the central

Visonic control panels equipped with powerlink 3 expose a JSON RPC API on port 8181.
This API is not documented. The objective of this repository is to gather information on this subject. The powerlink 3 works differently from previous versions. The following is therefore specific to this version.

**CAUTION: Read the safety information carefully**


## Compatibility

After testing, the function is available on the Powermaster 30 and 33 EXP G2 with firmware version 19 or 20.

## Security

Here is the list of security issues identified. Other problems may exist but are not listed here.

### HTTP
The API is only exposed in http. There is no exposed https port. As a result, all information transmitted between the alarm and the client is in the clear on the network and can therefore be intercepted and used by anyone with access to the network to which the powerlink is connected.

### User code
To operate the alarm requires a call for customer registration. This call requires transmitting the user code, i.e. allowing you to arm or disarm the alarm. This code is therefore transported in clear text.
Additionally, if someone accesses the registered machine, they can use the API without needing the user code.

It **seems** that the recording resource is not protected against an attack consisting of testing all possible codes one by one.

### Internet
It is highly recommended to block external incoming access to port 8181 of the alarm. This helps minimize the risk of attack from the internet.

## Problèmes connus

### Timeout
I don't know if this problem is specific to my installation but in certain cases, the API no longer responds, in particular via a client timeout or by an error return from the resource specifying a timeout.
As it stands, using the API is unreliable.

### Registered IPs
Only one IP can be registered at any given time. You cannot "de-register" an IP, you can just register another one as a replacement.
The registered IP has a lifespan of a few days.

## Usage

The exposed API is in JSON RPC. Refer to [specification](https://www.jsonrpc.org/specification).
Use requires, first of all, a call to the PmaxService/registerClient resource. This allows you to register an IP for API use.
Once the client is registered, it is possible to call on the resources.

## Errors
List of common errors
TODO

## Ressources
In the following, variables on {{in curly brackets}}.

  - ip: the ip of the alarm or its non-DNS
  - port: api http port: 8181

### Command list

This is the only resource that is not JSON RPC.
Customer registration required: no
Call: GET http://{{ip}}:{{port}}/remote/json-rpc
Back: The list of available resources
Errors: None known

## Image recovery

To retrieve images from detectors equipped with cameras, you must activate it via installer mode in the menus:
03: Central -> 80: DOM. TIER. PART -> activate
This allows you to open port 21 accessible in anonymous FTP

## FAQ

### What is the API URL?

    http://{{ip}}:8181/remote/json-rpc

### How to make a call to the alarm API?

You must start by registering the calling machine via the registerClient call

Example : 

    POST /remote/json-rpc HTTP/1.1
    Host: {{ip}}:8181
    Content-Type: application/json
    Content-Length: 116
    
    {
    	"params": ["{{ip_machine_appelante}}", {{code_alarme}}, "user"],
    	"jsonrpc": "2.0",
    	"method": "PmaxService/registerClient", 
    	"id":1
    }

with : 
{{ip}} : alarm ip
{{ip_machine_appelante}} : the IP of the calling machine. **MUST PUT AN IP, NO DNS**
{{alarm_code}}: the 4-digit code to activate the alarm. Rq: I don't know how codes starting with 0 are managed. You should try to put " if that doesn't work.

### Retrieve information from the control unit ###

Make a call on getPanelStatuses.
Example : 

    POST /remote/json-rpc HTTP/1.1
    Host: {{ip}}:8181
    Content-Type: application/json
    Content-Length: 91
    
    {
    	"params": null,
    	"jsonrpc": "2.0",
    	"method": "PmaxService/getPanelStatuses", 
    	"id":1
    }

### Arm/disarm a zone ###
Make a call on PmaxService/setPanelState
Exemple : 

    POST /setPanelState HTTP/1.1
    Host: {{ip}}:8181
    Content-Type: text/plain
    Content-Length: 136
    
    {
        "params": ["{{code_alarme}}", "{{etat}}", {{partition}}, true, true],
        "jsonrpc": "2.0",
        "method": "PmaxService/setPanelState", 
        "id":1
    }
with : 
{{code_alarme}} : the 4-digit code to activate the alarm. 
{{etat}} : the state of the alarm you want to activate : "AWAY", "HOME" (armement partiel) ou "DISARM"
{{partition}} ; le numéro de la partition : 1, 2 ou 3
Rq : I don't know how to do it on a non-zoned alarm


### What is the id in calls
cf https://www.jsonrpc.org/specification

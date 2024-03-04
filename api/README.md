# API information exposed by the powermaster 3

The power master 3 exposes a JSON RPC API on port 8181.
This API is not documented. The objective of this repository is to gather information on this subject. The powerlink 3 works differently from previous versions. The following is therefore specific to this version.

**WARNING: Read the safety information carefully**

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

## Activation

On some alarms, the API is enabled by default. On others, it must be activated via installer mode in the menus:
03: Central -> 80: DOM. TIER. PART -> activate


## Known issues

### Timeout
I don't know if this problem is specific to my installation but in certain cases, the API no longer responds, in particular via a client timeout or by an error return from the resource specifying a timeout.
As it stands, using the API is unreliable.

### Registered IPs
Only one IP can be registered at any given time. You cannot "de-register" an IP, you can just register another one as a replacement.
The registered IP has a lifespan of a few days.

## Use

The exposed API is in JSON RPC. Refer to [specification](https://www.jsonrpc.org/specification).
Use requires, first of all, a call to the PmaxService/registerClient resource. This allows you to register an IP for API use.
Once the client is registered, it is possible to call on the resources.


## Errors
List of common errors

TODO

## Resources

In the following, variables on {{in curly brackets}}.

  - ip: the ip of the alarm or its non-DNS
  - port: api http port: 8181


### List of commands
This is the only resource that is not JSON RPC.
Customer registration required: no
Call: GET http://{{ip}}:{{port}}/remote/json-rpc
Back: The list of available resources
Errors: None known


## Test postman
The repository's postman collection contains a set of non-exhaustive tests.
It is necessary to specialize it by modifying the values of the environment part of the postman.


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
     "params": ["{{calling_machine_ip}}", {{alarm_code}}, "user"],
     "jsonrpc": "2.0",
     "method": "PmaxService/registerClient",
     "id":1
     }

with :
{{ip}}: the IP of the alarm
{{ip_machine_appelante}}: the IP of the calling machine. **MUST PUT AN IP, NO DNS**
{{alarm_code}}: the 4-digit code to activate the alarm. Rq: I don't know how codes starting with 0 are managed. You should try to put " if that doesn't work.

### Retrieve information from the control panel ###
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
Make a call to PmaxService/setPanelState
Example :

     POST /setPanelState HTTP/1.1
     Host: {{ip}}:8181
     Content-Type: text/plain
     Content-Length: 136
    
     {
         "params": ["{{alarm_code}}", "{{status}}", {{partition}}, true, true],
         "jsonrpc": "2.0",
         "method": "PmaxService/setPanelState",
         "id":1
     }
with :
{{alarm_code}}: the 4-digit code to activate the alarm.
{{state}}: the state of the alarm that you want to activate: "AWAY", "HOME" (partial arming) or "DISARM"
{{partition}} ; the partition number: 1, 2 or 3
Rq: I don’t know how to do it on a non-zoned alarm


### What is the id in calls
see https://www.jsonrpc.org/specification
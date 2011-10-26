<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$string = '{
    "doc": {
        "info": {
            "document": "<document>...</document>",
            "externalMetadata": "",
            "submitter": "",
            "allowDistribution": "true",
            "allowSearch": "true",
            "calaisRequestID": "53bfd801-0b48-44b8-bfcb-cac7cc690312",
            "externalID": "",
            "id": "http://id.opencalais.com/TUbejyfbRoTIOh7oTkKA2Q"
        },
        "meta": {
            "submitterCode": "d98c1dd4-008f-04b2-e980-0998ecf8427e",
            "signature": "digestalg-1|UYnCz0tzGver5esfcsd5JOZUfF4=|jJsPKDCdta+80FRtjLMC3tK2SQaFQbFMM0u2Eg2sPUQkWj33Br98cg==",
            "contentType": "text/html",
            "emVer": "UnifiedIM-DJ",
            "langIdVer": "DefaultLangId",
            "language": "English",
            "processingVer": "CalaisJob01",
            "submissionDate": "2008-08-24 18:13:52.203",
            "messages": []
        }
    },
    "topics": {
        "http://d.opencalais.com/dochash-1/7b203398-b867-3595-ac40-2c0801e17bb2/cat/1": {
            "_typeGroup": "topics",
            "category": "http://d.opencalais.com/cat/Calais/BusinessFinance",
            "classifierName": "Calais",
            "categoryName": "Business_Finance",
            "score": 0.478
        }
    },
    "entities": {
        "Country": {
            "http://d.opencalais.com/genericHasher-1/398f0f7f-7b2f-3363-bda4-f09b836ed062": {
                "_type": "Country",
                "_typeGroup": "entities",
                "name": "Germany",
                "instances": [
                    {
                        "detection": "[ France and ]Germany[, and also India]",
                        "prefix": " France and ",
                        "exact": "Germany",
                        "suffix": ", and also India",
                        "offset": 5927,
                        "length": 7
                    }
                ],
                "relevance": 0.06,
                "resolutions": [
                    {
                        "name": "Germany",
                        "lat": "51.0",
                        "long": "9.0"
                    }
                ]
            }
        },
        "Company": {
            "http://d.opencalais.com/comphash-1/7f9f8e5d-782c-357a-b6f3-7a5321f92e13": {
                "_type": "Company",
                "_typeGroup": "entities",
                "name": "AT&T",
                "nationality": "N/A",
                "instances": [
                    {
                        "detection": "[been market chatter that Garmin was talking to ]AT&T[ , Reiner said, adding that the only other]",
                        "prefix": "been market chatter that Garmin was talking to ",
                        "exact": "AT&T",
                        "suffix": " , Reiner said, adding that the only other",
                        "offset": 2153,
                        "length": 8
                    }
                ],
                "relevance": 0.272,
                "resolutions": [
                    {
                        "score": 1,
                        "name": "AT&T Corp.",
                        "ticker": "T",
                        "webaddress": "http://www.att.com/"
                    }
                ]
            }
        }
    },
    "relations": {
        "ConferenceCall": {
            "http://d.opencalais.com/genericHasher-1/77723e3f-3683-3497-a13f-71dc4f3a719e": {
                "_type": "ConferenceCall",
                "_typeGroup": "relations",
                "company": <an object reference to company object from entities.Company section with id equals to http://d.opencalais.com/comphash-1/7f9f8e5d-782c-357a-b6f3-7a5321f92e13&...,
                "status": "announced",
                "instances": [
                    {
                        "detection": "[ the United States would have been T-Mobile</p><p>]In the conference call, the company said it[ expects to make carrier announcements in 2009. ]",
                        "prefix": " the United States would have been T-Mobile</p><p>",
                        "exact": "In the conference call, the company said it",
                        "suffix": " expects to make carrier announcements in 2009. ",
                        "offset": 2287,
                        "length": 43
                    }
                ]
            }
        }
    }
}
';
$data = json_decode($string);
echo $data;
?>

<?php

namespace Profounder\Services;

use Profounder\Exceptions\InvalidSession;

class IdentityPool
{
    /**
     * Sessions array of username, password and cookies.
     *
     * @var array
     */
    private $pool = [
        [
            'username' => 'tgvnlvbbbdthcibtrtdergtekdice@dispostable.com',
            'password' => '3M9Y8H5H1',
            'cookies'  => 'ASP.NET_SessionId=qeypllszgeipwcekmrfq1lnq; TBHFORMAUTH=C1016FC71D23E8117B151465882478FA45A989450A386B706200616B944BE44E74DEDF764125D9FA269C5FD43135B486BCE0EB9ADC1B919447DBA4A18650D79E52FFB84224DB8F6A21AA15ACD791311630A7E23BD051D6AB068CFC784EBEFBA3649DC6440AF2FE35EC810705300F8C4AE5873EF8B8C310DCECD27982DEDC65CB63414AE581ADEA1074A350F2904A7F68A4D6C7382FF8A16767BFBA371C412F4602B7512EE1AB4FD30AD3CE21C028C1840B7C5C6FB7B4D843957798B0152008212B0F0B77',
        ],
        [
            'username' => 'samsun1@mailinator.com',
            'password' => '123123asd',
            'cookies'  => 'ASP.NET_SessionId=hj1erf3aerdbrlfm3fxuk5u2; TBHFORMAUTH=2B6DEFFA25FC0D56A1ACCC3D8F91BF3B050862F9735802ABF92778BE1AC63A007798B76FB1D0C4D21723D2EF8B318189C3B1C5D320D461BCACE32A475B7BBD20253AD62564AD6D18FDB0D86739AA19CF0BE28022B3AF8D21816DDDB10F2DB6159E3C0C73E2F1033676ABF6C94B286253F8B3F8C4877AAB1A35FD829D9A5A636A411888C94AEA53E5B178279E10F1D1109C9BB8F9',
        ],
        [
            'username' => 'vivaldi@mailinator.com',
            'password' => '123123asd',
            'cookies'  => 'ASP.NET_SessionId=3lmy5nk3kpxisfkocjoms2yq; optimizelyEndUserId=oeu1485072127025r0.4122511440168102; hsfirstvisit=http%3A%2F%2Fwww.profound.com%2FHome.aspx%3FReturnUrl%3D%252f||1485072130916; hsfirstvisit=http%3A%2F%2Fwww.profound.com%2FHome.aspx%3FReturnUrl%3D%252f||1485072130916; tosDeclineCount=count=0; popup=1; user_Name=vivaldi@mailinator.com; optimizelySegments=%7B%222016600097%22%3A%22direct%22%2C%222018230111%22%3A%22false%22%2C%222020310110%22%3A%22unknown%22%7D; optimizelyBuckets=%7B%7D; _ga=GA1.2.466033925.1485072129; __hstc=63860784.10369bbfb4e5e77aad06bcbf5e852e86.1485072130934.1485085977459.1485088419455.3; __hssrc=1; hubspotutk=10369bbfb4e5e77aad06bcbf5e852e86; _we_wk_ss_lsf_=true; TBHFORMAUTH=1CA5EFA6407B737D15E07DC4EEBDD4C9BDC4AB5890E64F5D02F8352B493BBF2510B17E59DBBB5885AD6F8882F9943EF8B42EE6EF33C2084538FBFD93B43974AC590A43E82724791613D6DB354EB8C54062D70CC889F3314960B3EA34E2BA0AFD24B2DA72CEA290D155EAC56731A97115398FA9A31D355E83FE7DC5B53946104AC8A18F41B6AAC8E4C951C9AF2576399273586374',
        ],
        [
            'username' => 'canary@mailinator.com',
            'password' => '123123asd',
            'cookies'  => 'ASP.NET_SessionId=vh2beynxxkyzp0jqcacfl1mc; TBHFORMAUTH=15B53EE7CBC0AC4DFE128594A2601FC1947608FF5F8F0CCF91AE7A08EE85A7CEEE65176F047AF7F7CC4DE38348CABDA2A5C57784E930210054E88D001E406A472CE730E7528285E0A532FE365F97B035913A2DE69CD33BA81DD56ADE413D227B17A20220D435F7B3E65F52BA60CC929C9A9CDD769C1FAAEE6B25882E3ECA45D8428F1D748869CCEBDD0727871F089B328135E837',
        ],
    ];

    /**
     * Returns a session object by ID.
     *
     * @param  int $id
     *
     * @return object
     *
     * @throws InvalidSession
     */
    public function retrieve(int $id)
    {
        if (isset($this->pool[$id])) {
            return (object) $this->pool[$id];
        }

        throw new InvalidSession('Out of identity sessions. Add more sessions or reduce workers.');
    }

    /**
     * Retrieves a random session.
     *
     * @return object
     */
    public function random()
    {
        return $this->retrieve(array_rand($this->pool));
    }
}

<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Application\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Ticket implements InputFilterAwareInterface
{
    protected $subject;
    protected $description;
    protected $importance;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->subject     = isset($data['subject'])     ? $data['subject']     : null;
        $this->description = isset($data['description']) ? $data['description'] : null;
        $this->importance  = isset($data['importance'])  ? $data['importance']  : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();
        $factory     = new InputFactory();

        $inputFilter->add(
            $factory->createInput([
                'name'       => 'subject',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
                'validators' => [
                    ['name' => 'not_empty'],
                    [
                        'name' => 'string_length',
                        'options' => [
                            'min' => 5,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name'       => 'description',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'not_empty'],
                    [
                        'name' => 'string_length',
                        'options' => [
                            'min' => 30,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name'     => 'importance',
                'required' => true,
            ])
        );

        $this->inputFilter = $inputFilter;

        return $this->inputFilter;
    }
}

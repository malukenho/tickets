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

namespace Application\Command\Ticket;

class OpenNewTicket
{
    /**
     * @var string
     */
    private $subject;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $importance;
    /**
     * @var string
     */
    private $openedBy;
    /**
     * @var string
     */
    private $projectId;

    /**
     * Constructor.
     *
     * @param string $subject
     * @param string $description
     * @param string $importance
     * @param string $projectId
     * @param string $openedBy
     */
    public function __construct(
        $subject,
        $description,
        $importance,
        $projectId,
        $openedBy
    ) {
        $this->subject     = (string) $subject;
        $this->description = $description;
        $this->importance  = $importance;
        $this->projectId   = $projectId;
        $this->openedBy    = $openedBy;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getImportance()
    {
        return $this->importance;
    }

    /**
     * @return string
     */
    public function getOpenedBy()
    {
        return $this->openedBy;
    }

    /**
     * @return string
     */
    public function getProjectId()
    {
        return $this->projectId;
    }
}

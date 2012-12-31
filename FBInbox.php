<?php



class FBInbox {

 

    private $_uid; // Logged in user id

    public $facebook;

 

    /**
     * Constructor - Setup application credentials
     *
     * The constructor need to be called with the API key, API secret. It returns the logged in user id
     */

    public function __construct($objectFB) {

        $this->facebook = $objectFB;

        $this->_uid = $this->facebook->require_login();

    }

 

    // --------------------------------------------------------------------

 

  /**
     * Get user's extended permission for reading inbox
    *
 * @access  private
     * @return  boolean
     */

    private  function _getExtendedPermission() {

        if (!$this->facebook->api_client->users_hasAppPermission("read_mailbox")) {

            echo '<fb:prompt-permission perms="read_mailbox">Read Mailbox</fb:prompt-permission>';

       }else {

            return true;

        }

    }

 

   // --------------------------------------------------------------------

 

  /**
     * Get user's folder informations
     *
     * @access  public
    * @return  Array
   */

    public function getFolderInfo() {

        if($this->_getExtendedPermission()) {

            $fql = "SELECT folder_id, name, unread_count, viewer_id FROM mailbox_folder WHERE viewer_id={$this->_uid}";

            $status = $this->facebook->api_client->fql_query($fql);

           return $status;

        }else {

            return false;

        }

    }

 

    // --------------------------------------------------------------------

 

    /**
     * Get user's extended permission for reading inbox
     * Currently, all users only have three Inbox folders: Inbox (folder_id = 0), Outbox (folder_id = 1), and Updates (folder_id = 4)
   * @access  public
     * @param $folderId Integer
     * @return  Array
    */

    public function getAllThreads($folderId = 0) {

        if($this->_getExtendedPermission()) {

            $fql = "SELECT thread_id, folder_id, subject, recipients, updated_time, parent_message_id, parent_thread_id, message_count, snippet, snippet_author, object_id, unread, viewer_id FROM thread WHERE folder_id = $folderId AND viewer_id={$this->_uid} LIMIT 20";

            $threads = $this->facebook->api_client->fql_query($fql);

            return $threads;

        }else {

            return false;

        }

    }

 

    // --------------------------------------------------------------------

 

    /**
     * Get user's extended permission for reading inbox
     * Currently, all users only have three Inbox folders: Inbox (folder_id = 0), Outbox (folder_id = 1), and Updates (folder_id = 4)
     * @access  public
     * @param $folderId Integer
     * @return  Array
     */

    public function getUnreadThreads($folderId = 0) {

        if($this->_getExtendedPermission()) {

            $fql = "SELECT thread_id, folder_id, subject, recipients, updated_time, parent_message_id, parent_thread_id, message_count, snippet, snippet_author, object_id, unread, viewer_id FROM thread WHERE folder_id = $folderId AND viewer_id={$this->_uid} AND unread != 0 LIMIT 20";

            $threads = $this->facebook->api_client->fql_query($fql);

            return $threads;

        }else {

            return false;

        }

    }

 

    // --------------------------------------------------------------------

 

    /**
     * Get user's extended permission for reading inbox
     * Currently, all users only have three Inbox folders: Inbox (folder_id = 0), Outbox (folder_id = 1), and Updates (folder_id = 4)
     *
    * @access  public
     * @param $key String
     * @param $folderId Integer
     * @return  Array
     */

    public function searchInbox($key, $folderId = 0) {

        if($this->_getExtendedPermission()) {

            $fql = "SELECT thread_id, folder_id, subject, recipients, updated_time, parent_message_id, parent_thread_id, message_count, snippet, snippet_author, object_id, unread, viewer_id FROM thread WHERE folder_id = $folderId AND viewer_id={$this->_uid} AND CONTAINS('$key') LIMIT 20";

            $threads = $this->facebook->api_client->fql_query($fql);

            return $threads;

        }else {

            return false;

        }

    }

 

    // --------------------------------------------------------------------

 

    /**
     * Get user's extended permission for reading inbox    
     * Currently, all users only have three Inbox folders: Inbox (folder_id = 0), Outbox (folder_id = 1), and Updates (folder_id = 4)
     *
     * @access  public
    * @param $threadId Integer
     * @param $messageId Integer
     * @return  Array
     */

    public function readMessage($threadId, $messageId = NULL) {

        if($this->_getExtendedPermission()) {

            if(NULL == $messageId) {

                $fql = "SELECT message_id, thread_id, author_id, body, created_time, viewer_id FROM message WHERE thread_id = $threadId";

            }else {

                $fql = "SELECT message_id, thread_id, author_id, body, created_time, viewer_id FROM message WHERE thread_id = $threadId  AND message_id = $messageId";

            }

            $message = $this->facebook->api_client->fql_query($fql);

          return $message;

        }else {

            return false;

       }

  }

}
?>
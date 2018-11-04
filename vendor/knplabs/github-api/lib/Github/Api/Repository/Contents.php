<?php

namespace Github\Api\Repository;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;
use Github\Exception\ErrorException;
use Github\Exception\InvalidArgumentException;
use Github\Exception\MissingArgumentException;
use Github\Exception\TwoFactorAuthenticationRequiredException;

/**
 * @link   http://developer.github.com/v3/repos/contents/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Contents extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * Configure the body type.
     *
     * @link https://developer.github.com/v3/repo/contents/#custom-media-types
     *
     * @param string|null $bodyType
     *
     * @return self
     */
    public function configure($bodyType = null)
    {
        if (!in_array($bodyType, ['html', 'object'])) {
            $bodyType = 'raw';
        }

        $this->acceptHeaderValue = sprintf('application/vnd.github.%s.%s', $this->client->getApiVersion(), $bodyType);

        return $this;
    }

    /**
     * Get content of README file in a repository.
     *
     * @link http://developer.github.com/v3/repos/contents/
     *
     * @param string      $username   the user who owns the repository
     * @param string      $repository the name of the repository
     * @param null|string $reference  reference to a branch or commit
     *
     * @return array information for README file
     */
    public function readme($username, $repository, $reference = null)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/readme', [
            'ref' => $reference,
        ]);
    }

    /**
     * Get contents of any file or directory in a repository.
     *
     * @link http://developer.github.com/v3/repos/contents/
     *
     * @param string      $username   the user who owns the repository
     * @param string      $repository the name of the repository
     * @param null|string $path       path to file or directory
     * @param null|string $reference  reference to a branch or commit
     *
     * @return array|string information for file | information for each item in directory
     */
    public function show($username, $repository, $path = null, $reference = null)
    {
        $url = '/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/contents';
        if (null !== $path) {
            $url .= '/'.rawurlencode($path);
        }

        return $this->get($url, [
            'ref' => $reference,
        ]);
    }

    /**
     * Creates a new file in a repository.
     *
     * @link http://developer.github.com/v3/repos/contents/#create-a-file
     *
     * @param string      $username   the user who owns the repository
     * @param string      $repository the name of the repository
     * @param string      $path       path to file
     * @param string      $content    contents of the new file
     * @param string      $message    the commit message
     * @param null|string $branch     name of a branch
     * @param null|array  $committer  information about the committer
     *
     * @throws MissingArgumentException
     *
     * @return array information about the new file
     */
    public function create($username, $repository, $path, $content, $message, $branch = null, array $committer = null)
    {
        $url = '/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/contents/'.rawurlencode($path);

        $parameters = [
          'content' => base64_encode($content),
          'message' => $message,
        ];

        if (null !== $branch) {
            $parameters['branch'] = $branch;
        }

        if (null !== $committer) {
            if (!isset($committer['name'], $committer['email'])) {
                throw new MissingArgumentException(['name', 'email']);
            }
            $parameters['committer'] = $committer;
        }

        return $this->put($url, $parameters);
    }

    /**
     * Checks that a given path exists in a repository.
     *
     * @param string      $username   the user who owns the repository
     * @param string      $repository the name of the repository
     * @param string      $path       path of file to check
     * @param null|string $reference  reference to a branch or commit
     *
     * @return bool
     */
    public function exists($username, $repository, $path, $reference = null)
    {
        $url = '/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/contents';

        if (null !== $path) {
            $url .= '/'.rawurlencode($path);
        }

        try {
            $response = $this->head($url, [
                'ref' => $reference,
            ]);

            if ($response->getStatusCode() != 200) {
                return false;
            }
        } catch (TwoFactorAuthenticationRequiredException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * Updates the contents of a file in a repository.
     *
     * @link http://developer.github.com/v3/repos/contents/#update-a-file
     *
     * @param string      $username   the user who owns the repository
     * @param string      $repository the name of the repository
     * @param string      $path       path to file
     * @param string      $content    contents of the new file
     * @param string      $message    the commit message
     * @param string      $sha        blob SHA of the file being replaced
     * @param null|string $branch     name of a branch
     * @param null|array  $committer  information about the committer
     *
     * @throws MissingArgumentException
     *
     * @return array information about the updated file
     */
    public function update($username, $repository, $path, $content, $message, $sha, $branch = null, array $committer = null)
    {
        $url = '/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/contents/'.rawurlencode($path);

        $parameters = [
          'content' => base64_encode($content),
          'message' => $message,
          'sha'     => $sha,
        ];

        if (null !== $branch) {
            $parameters['branch'] = $branch;
        }

        if (null !== $committer) {
            if (!isset($committer['name'], $committer['email'])) {
                throw new MissingArgumentException(['name', 'email']);
            }
            $parameters['committer'] = $committer;
        }

        return $this->put($url, $parameters);
    }

    /**
     * Deletes a file from a repository.
     *
     * @link http://developer.github.com/v3/repos/contents/#delete-a-file
     *
     * @param string      $username   the user who owns the repository
     * @param string      $repository the name of the repository
     * @param string      $path       path to file
     * @param string      $message    the commit message
     * @param string      $sha        blob SHA of the file being deleted
     * @param null|string $branch     name of a branch
     * @param null|array  $committer  information about the committer
     *
     * @throws MissingArgumentException
     *
     * @return array information about the updated file
     */
    public function rm($username, $repository, $path, $message, $sha, $branch = null, array $committer = null)
    {
        $url = '/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/contents/'.rawurlencode($path);

        $parameters = [
          'message' => $message,
          'sha'     => $sha,
        ];

        if (null !== $branch) {
            $parameters['branch'] = $branch;
        }

        if (null !== $committer) {
            if (!isset($committer['name'], $committer['email'])) {
                throw new MissingArgumentException(['name', 'email']);
            }
            $parameters['committer'] = $committer;
        }

        return $this->delete($url, $parameters);
    }

    /**
     * Get content of archives in a repository.
     *
     * @link http://developer.github.com/v3/repos/contents/
     *
     * @param string      $username   the user who owns the repository
     * @param string      $repository the name of the repository
     * @param string      $format     format of archive: tarball or zipball
     * @param null|string $reference  reference to a branch or commit
     *
     * @return string repository archive binary data
     */
    public function archive($username, $repository, $format, $reference = null)
    {
        if (!in_array($format, ['tarball', 'zipball'])) {
            $format = 'tarball';
        }

        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/'.rawurlencode($format).
            ((null !== $reference) ? ('/'.rawurlencode($reference)) : ''));
    }

    /**
     * Get the contents of a file in a repository.
     *
     * @param string      $username   the user who owns the repository
     * @param string      $repository the name of the repository
     * @param string      $path       path to file
     * @param null|string $reference  reference to a branch or commit
     *
     * @throws InvalidArgumentException If $path is not a file or if its encoding is different from base64
     * @throws ErrorException           If $path doesn't include a 'content' index
     *
     * @return null|string content of file, or null in case of base64_decode failure
     */
    public function download($username, $repository, $path, $reference = null)
    {
        $file = $this->show($username, $repository, $path, $reference);

        if (!isset($file['type']) || 'file' !== $file['type']) {
            throw new InvalidArgumentException(sprintf('Path "%s" is not a file.', $path));
        }

        if (!isset($file['content'])) {
            throw new ErrorException(sprintf('Unable to access "content" for file "%s" (possible keys: "%s").', $path, implode(', ', array_keys($file))));
        }

        if (!isset($file['encoding'])) {
            throw new InvalidArgumentException(sprintf('Can\'t decode content of file "%s", as no encoding is defined.', $path));
        }

        if ('base64' !== $file['encoding']) {
            throw new InvalidArgumentException(sprintf('Encoding "%s" of file "%s" is not supported.', $file['encoding'], $path));
        }

        return base64_decode($file['content']) ?: null;
    }
}

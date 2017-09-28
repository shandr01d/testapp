<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * 
 * @UniqueEntity(fields="email", message="user.login.unique")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @Groups({"users"})
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *      message = "user.name.not_blank"
     * )
     * @Assert\Length(
     *      min = "2",
     *      max = "40",
     *      minMessage = "user.name.min_length",
     *      maxMessage = "user.name.max_length"
     * )
     * 
     * @ORM\Column(name="name", type="string", length=40)
     * 
     * @Groups({"users"})
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *      message = "user.surname.not_blank"
     * )
     * 
     * @Assert\Length(
     *      min = "2",
     *      max = "40",
     *      minMessage = "user.surname.min_length",
     *      maxMessage = "user.surname.max_length"
     * )
     * 
     * @ORM\Column(name="surname", type="string", length=40)
     * 
     * @Groups({"users"})
     */
    private $surname;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *      message = "user.email.not_blank"
     * )
     * 
     * @Assert\Email(
     *     message = "user.email.wrong",
     *     checkMX = true
     * )
     * 
     * @ORM\Column(name="email", type="string", length=80, unique=true)
     * 
     * @Groups({"users"})
     */
    private $email;

    /**
     * @var string
     * 
     * @Assert\NotBlank(
     *      message = "user.phone.not_blank"
     * )
     * 
     * @Assert\Length(
     *     min = 8, 
     *     max = 20, 
     *     minMessage = "user.phone.min_length", 
     *     maxMessage = "user.phone.max_length"
     * )
     * 
     * @Assert\Regex(
     *     pattern="/^[+\d{7,}]$/",
     *     match=false,
     *     message="user.phone.regex"
     * )
     * 
     * @ORM\Column(name="phone", type="string", length=20)
     * 
     * @Groups({"users"})
     */
    private $phone;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *      message = "user.password.not_blank"
     * )
     * 
     * @Assert\Length(
     *      min = "6",
     *      max = "40",
     *      minMessage = "user.password.min_length",
     *      maxMessage = "user.password.max_length"
     * )
     * 
     * @ORM\Column(name="password", type="string", length=64)
     * 
     */
    private $password;
    
     /**
      * @ORM\Column(name="is_active", type="boolean")
      * 
      * @Groups({"users"})
      * 
     */
    private $isActive;
    
    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="user_roles")
     */
    private $roles;
    
    public function __construct()
    {
        $this->isActive = true;
        $this->roles = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
        
    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
    
    public function getSalt()
    {
        return null;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->name, 
            $this->password,
            $this->isActive
        ));
    }
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->name, 
            $this->password,
            $this->isActive
        ) = unserialize($serialized);
    }

    public function eraseCredentials() {}

    public function getUsername() {
        return $this->email;
    }
    
    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }
    
    
    /**
     * Add roles
     *
     * @param  $roles
     * @return User
     */
    public function addRole(Role $roles)
    {
        $this->roles[] = $roles;
    
        return $this;
    }

    /**
     * Remove roles
     *
     * @param Role $roles
     */
    public function removeRole(Role $roles)
    {
        $this->roles->removeElement($roles);
    }
    
    public function __toString() {
        return $this->name;
    }
    
}

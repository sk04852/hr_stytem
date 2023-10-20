import React,{ useEffect, useState } from 'react'
import { Input, Label } from 'reactstrap'
import ProfileData from '../../../data/data'
import UserTagData from '../../../data/userTag'
import './user.css'
var skillArray = []
function Profile () {
  const [editProfile, setEditProfile] = useState(false)
  const [userTagHashtag, setUserTagHastags] = useState([])
  const [profileData, setProfileData] = useState([])
  const editRecord = () => {
    setEditProfile(!editProfile)
  }
  useEffect(() => {
    setUserTagHastags(UserTagData)
    setProfileData(ProfileData)
    ProfileData.forEach(item => {
      setEId(item.Id)
      setName(item.name)
      setConsent(item.consent)
      setDateOfBirth(item.date_of_birth)
      setDateOfJoining(item.date)
      setLocation(item.location)
      setUserTag(item.user_tag)
      setSource(item.source)
      setDescription(item.description)
      setEducation(item.education)
      setEmail(item.email)
      setGender(item.gender)
      setJobHistory(item.job_history)
      setLanguage(item.language)
      setLanguageLevel(item.language_level)
      setOnwer(item.onwer)
      setNewsletter(item.newsletter)
      setPhone(item.phone)
      setPhoto(item.photo)
    })
  }, [])
  const [name, setName] = useState('')
  const [Eid, setEId] = useState(0)
  const [photo, setPhoto] = useState('')
  const [email, setEmail] = useState('')
  const [phone, setPhone] = useState('')
  const [gender, setGender] = useState('')
  const [dateOfBirth, setDateOfBirth] = useState('')
  const [location, setLocation] = useState('')
  const [dateOfJoining, setDateOfJoining] = useState('')
  const [onwer, setOnwer] = useState('')
  const [userTag, setUserTag] = useState([])
  const [description, setDescription] = useState('')
  const [source, setSource] = useState('')
  const [jobHistory, setJobHistory] = useState('')

  const [language, setLanguage] = useState('')
  const [languageLevel, setLanguageLevel] = useState('')
  const [consent, setConsent] = useState(false)
  const [newsletter, setNewsletter] = useState(false)
  const [education, setEducation] = useState('')

  const saveRecord = () => {
    console.log(newsletter)
    setEditProfile(!editProfile)
    const newState = profileData.map(item => {
      return {
        ...item,
        Id: Eid,
        photo: photo,
        name: name,
        email: email,
        phone: phone,
        gender: gender,
        date_of_birth: dateOfBirth,
        location: location,
        date: dateOfJoining,

        onwer: onwer,
        user_tag: userTag,
        description: description,
        source: source,
        job_history: jobHistory,
        skills: [],
        language_level: languageLevel,
        language: language,
        consent: consent,
        newsletter: newsletter,
        education: education
      }
    })
    console.log(newState)
    setProfileData(newState)
  }
  const updateName = e => {
    console.log(e.target.value)
    setName(e.target.value)
  }

  const updateEid = e => {
    console.log(e.target.value)
    setEId(parseInt(e.target.value))
  }

  const updateEmail = e => {
    console.log(e.target.value)
    setEmail(e.target.value)
  }

  const updatePhoto = e => {
    console.log(e.target.files)
    const [file] = e.target.files
    setPhoto(URL.createObjectURL(file))
  }

  const updatePhone = e => {
    console.log(e.target.value)
    setPhone(e.target.value)
  }

  const updateGender = e => {
    console.log(e.target.value)
    setGender(e.target.value)
  }

  const updateDateOfBirth = e => {
    console.log(e.target.value)
    setDateOfBirth(e.target.value)
  }

  const updateLocation = e => {
    console.log(e.target.value)
    setLocation(e.target.value)
  }

  const updateDateOfJoining = e => {
    console.log(e.target.value)
    setDateOfJoining(e.target.value)
  }

  const updateOnwer = e => {
    console.log(e.target.value)
    setOnwer(e.target.value)
  }

  // const updateUserTag = e => {
  //   console.log(e.target.value)
  //   setUserTag(e.target.value)
  // }
  const addHashtTag = id => {
    userTagHashtag.forEach(item => {
      if (item.id === id) {
        skillArray.push(item.name)
      }
      setUserTag(skillArray)
    })
    const newState = userTagHashtag.map(item => {
      if (item.id === id) {
        return {
          ...item,
          status: true
        }
      }
      return item
    })
    console.log(newState)
    setUserTagHastags(newState)
  }
  const updateDescription = e => {
    console.log(e.target.value)
    setDescription(e.target.value)
  }

  const updateSource = e => {
    console.log(e.target.value)
    setSource(e.target.value)
  }

  const updateJobHistory = e => {
    console.log(e.target.value)
    setJobHistory(e.target.value)
  }

  const updateLanguage = e => {
    console.log(e.target.value)
    setLanguage(e.target.value)
  }

  const updateLanguageLevel = e => {
    console.log(e.target.value)
    setLanguageLevel(e.target.value)
  }

  const updateEducation = e => {
    console.log(e.target.value)
    setEducation(e.target.value)
  }

  const updateConsent = e => {
    setConsent(e.target.checked)
  }

  const updateNewletter = e => {
    console.log(e.target.checked)
    setNewsletter(e.target.checked)
  }

  const deleteHastag = id => {
    console.log(id)
    setUserTagHastags(current =>
      current.filter(item => {
        return item.id !== id
      })
    )
  }

  return (
    <>
      <div class='card'>
        <div class='card-header float-right'>
          <h1>Personal Information</h1>
          <div class='pointer' onClick={editRecord}>
            <i class='icon-pencil ml-3 '></i>
          </div>
        </div>

        <div class='card-body'>
          {!editProfile ? (
            <div class='container'>
              {profileData.map(item => (
                <div class='row'>
                  <div class='col-sm-2  '>
                    <img src={item.photo} alt='Avatar' class='avatardesign' />
                  </div>
                  <div class='col-sm-3'>
                    <div>
                      <p>Name:</p>
                      <p>Email:</p>
                      <p>Employee ID:</p>
                      <p>Phone: </p>
                      <p>Gender: </p>
                      <p>Date of Birth: </p>
                      <p>Location: </p>
                      <p>Date of Joining: </p>

                      <p> Report To: </p>
                      <p>Consent</p>
                      <p>Newsletter</p>
                      <p>description:</p>
                      <p>Source:</p>
                      <p>User Tag:</p>
                    </div>
                  </div>

                  <div class='col-sm-5'>
                    <p>{item.name}</p>
                    <p>{item.email}</p>
                    <p> {item.Id}</p>
                    <p>{item.phone}</p>
                    <p> {item.gender}</p>
                    <p> {item.date_of_birth}</p>
                    <p> {item.location}</p>
                    <p> {item.date}</p>

                    <p>{item.onwer}</p>
                    <p class='form-check'>
                      {' '}
                      <input
                        class='form-check-input'
                        type='checkbox'
                        value=''
                        id='flexCheckDefault'
                        checked={item.consent}
                      />
                      <label
                        class='form-check-label'
                        for='flexCheckDefault'
                      ></label>
                    </p>
                    <p class='form-check'>
                      {' '}
                      <input
                        class='form-check-input'
                        type='checkbox'
                        value=''
                        id='flexCheckDefault'
                        checked={item.newsletter}
                      />
                      <label
                        class='form-check-label'
                        for='flexCheckDefault'
                      ></label>
                    </p>
                    <p>{item.description}</p>
                    <p> {item.source}</p>
                    <p>
                      <div class='row'>
                        <div class='col-sm-12'>
                          {item.user_tag.map(items => (
                            <div>
                              <a href='#' class='btn btn-primary mb-2'>
                                {items}
                              </a>
                            </div>
                          ))}
                        </div>
                      </div>
                    </p>
                  </div>
                  <div class='col-sm-2'>
                    <div className='mt-2'>
                      <a href='#' class='btn btn-primary'>
                        Upload CV
                      </a>
                    </div>
                    <div className='mt-2'>
                      <div class='dropdown'>
                        <button
                          class='btn btn-secondary dropdown-toggle'
                          type='button'
                          id='dropdownMenuButton'
                          data-toggle='dropdown'
                          aria-haspopup='true'
                          aria-expanded='false'
                        >
                          Action
                        </button>
                        <div
                          class='dropdown-menu'
                          aria-labelledby='dropdownMenuButton'
                        >
                          <a class='dropdown-item' href='#'>
                            Apply for job
                          </a>
                          <a class='dropdown-item' href='#'>
                            Send interview info
                          </a>
                          <a class='dropdown-item' href='#'>
                            Move to 1st interview
                          </a>
                          <a class='dropdown-item' href='#'>
                            Move to 2nd interview
                          </a>
                          <a class='dropdown-item' href='#'>
                            Make offer
                          </a>
                          <a class='dropdown-item' href='#'>
                            Make placement
                          </a>
                          <a class='dropdown-item' href='#'>
                            Reject candidate
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div class='row'>
              <div class='col-sm-4'>
                <img src={photo} alt='Avatar' class='avatardesign mb-3' />
                <Input
                  type='file'
                  name='photo'
                  id='exampleEmail'
                  placeholder='Image'
                  onChange={updatePhoto}
                />
              </div>
              <div class='col-sm-4'>
                <Label>Name</Label>
                <Input
                  type='text'
                  name='name'
                  placeholder='name'
                  defaultValue={name}
                  onChange={updateName}
                />
                <Label>Email</Label>
                <Input
                  type='email'
                  name='email'
                  placeholder='Email'
                  defaultValue={email}
                  onChange={updateEmail}
                />
                <Label>Employee Id</Label>
                <Input
                  type='number'
                  name='id'
                  placeholder='Employee Id'
                  defaultValue={Eid}
                  onChange={updateEid}
                />

                <Label>Phone Number</Label>
                <Input
                  type='text'
                  name='phone'
                  placeholder='Phone Number'
                  defaultValue={phone}
                  onChange={updatePhone}
                />
                <Label>Gender</Label>
                <Input
                  type='text'
                  name='gender'
                  placeholder='Gender'
                  defaultValue={gender}
                  onChange={updateGender}
                />
                <Label>Date of Birth</Label>
                <Input
                  type='date'
                  name='date_of_birth'
                  placeholder='Date of Birth'
                  defaultValue={dateOfBirth}
                  onChange={updateDateOfBirth}
                />
                <Label>Location</Label>
                <Input
                  type='text'
                  name='location'
                  placeholder='Location '
                  defaultValue={location}
                  onChange={updateLocation}
                />
              </div>
              <div class='col-sm-4'>
                <Label>Date of Joining</Label>
                <Input
                  type='date'
                  name='date_of_joining'
                  placeholder='Date of Joining'
                  defaultValue={dateOfJoining}
                  onChange={updateDateOfJoining}
                />

                <Label>Onwer</Label>
                <Input
                  type='text'
                  name='onwer'
                  placeholder='Onwer'
                  defaultValue={onwer}
                  onChange={updateOnwer}
                />
                <Label className='ml-3 mt-2'>
                  <Input
                    type='checkbox'
                    defaultChecked={consent}
                    onChange={updateConsent}
                    value={consent}
                  />{' '}
                  Consent
                </Label>
                <Label check className='ml-3 mt-2'>
                  <Input
                    type='checkbox'
                    defaultChecked={newsletter}
                    onChange={updateNewletter}
                    value={newsletter}
                  />{' '}
                  Newsletter
                </Label>
                <Label>Description</Label>
                <Input
                  type='text'
                  name='description'
                  placeholder='Description'
                  defaultValue={description}
                  onChange={updateDescription}
                />

                <Label>Source</Label>
                <Input
                  type='text'
                  name='source'
                  placeholder='Source'
                  defaultValue={source}
                  onChange={updateSource}
                />
                <Label>User Tag</Label>
                <div class='row'>
                  {userTagHashtag?.map(item => (
                    <div class='col-sm-12  mb-3'>
                      <button
                        class='btn btn-primary'
                        onClick={() => addHashtTag(item.id)}
                        disabled={item.status}
                      >
                        {item.name}
                      </button>
                      {!item.status && (
                        <span
                          onClick={() => deleteHastag(item.id)}
                          class='pointer'
                        >
                          <i class='fa fa-times ml-2' aria-hidden='true'></i>
                        </span>
                      )}
                    </div>
                  ))}
                </div>
                <div>
                  <button
                    class='btn btn-primary float-right mt-3'
                    onClick={saveRecord}
                  >
                    Save
                  </button>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>{' '}
    </>
  )
}

export default Profile

import React,{ useEffect, useState } from 'react'
import {
    Button, Input, Label, Modal, ModalBody,
    ModalFooter
} from 'reactstrap'
import './user.css'
var languageArray = []
function Language () {
  const [editProfile, setEditProfile] = useState(false)
  const [possibleLanguage, setPossibleLanguages] = useState([
    'English',
    'French',
    'Spanish'
  ])
  const [profileData, setProfileData] = useState([
    {
      language: 'English',
      language_level: 'A1'
    }
  ])
  const editRecord = () => {
    setEditProfile(!editProfile)
  }
  useEffect(() => {
    profileData.forEach(item => {
      setLanguage(item.language)
      setLanguageLevel(item.language_level)
    })
  }, [])

  const [language, setLanguage] = useState('')
  const [languageLevel, setLanguageLevel] = useState('')

  const [open, setOpen] = useState(false)
  const openModal = () => setOpen(!open)
  const saveRecord = () => {
    setEditProfile(!editProfile)
    const newState = profileData.map(item => {
      return {
        ...item,

        language_level: languageLevel,
        language: language
      }
    })
    console.log(newState)
    setProfileData(newState)
  }

  const updateLanguage = e => {
    console.log(e.target.value)
    setLanguage(e.target.value)
  }

  const updateLanguageLevel = e => {
    console.log(e.target.value)
    setLanguageLevel(e.target.value)
  }

  const addNewLanguage = () => {
    languageArray = possibleLanguage
    languageArray.push(language)
    setPossibleLanguages(languageArray)
    setOpen(false)
  }
  return (
    <>
      <div class='card'>
        <div class='card-header float-right'>
          <h1>Language</h1>
          <div class='pointer' onClick={editRecord}>
            <i class='icon-pencil ml-3 '></i>
          </div>
        </div>

        <div class='card-body'>
          {!editProfile ? (
            <div class='container'>
              {profileData.map(item => (
                <div class='row'>
                  <div class='col-sm-12'>
                    <span>Location: {item.language}</span>-{' '}
                    <span>{item.language_level}</span>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div class='row'>
              <div class='col-sm-6'>
                <Label for='exampleSelect'>Select Language</Label>
                <Input
                  type='select'
                  name='language'
                  id='exampleSelect'
                  onChange={updateLanguage}
                >
                  <option>Select Language</option>
                  {possibleLanguage.map(item => (
                    <option value={item}>{item}</option>
                  ))}
                </Input>
                <Label for='exampleSelect'>Select Language Level</Label>
                <Input
                  type='select'
                  name='level'
                  id='exampleSelect'
                  onClick={updateLanguageLevel}
                >
                  <option>Select Level</option>
                  <option value={'A1'}>A1</option>

                  <option value={'A2'}>A2</option>
                  <option value={'B1'}> B1</option>
                  <option value={'B2'}>B2</option>
                  <option value={'C1'}>C1</option>
                  <option value={'C2'}>C2</option>
                </Input>

                <button
                  class='btn btn-primary float-right mt-3'
                  onClick={saveRecord}
                >
                  Save
                </button>
              </div>
              <div class='col-sm-3'>
                <div>
                  <button
                    class='btn btn-primary float-right'
                    onClick={openModal}
                  >
                    Add Language
                  </button>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>{' '}
      <Modal isOpen={open} toggle={openModal}>
        <ModalBody>
          {' '}
          <Label>Add Language</Label>
          <Input
            type='text'
            name='hashtag'
            placeholder='Language'
            onChange={updateLanguage}
          />
        </ModalBody>
        <ModalFooter>
          <Button color='secondary' onClick={addNewLanguage}>
            Save
          </Button>
        </ModalFooter>
      </Modal>
    </>
  )
}

export default Language

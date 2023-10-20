import React,{ useEffect, useState } from 'react'
import {
  Button, Input, Label, Modal, ModalBody,
  ModalFooter
} from 'reactstrap'
import ProfileData from '../../../data/data'
import SkillData from '../../../data/skills'
import './user.css'
var skillArray = []
var skillHashArray = []
function Skills () {
  const [editProfile, setEditProfile] = useState(false)
  const [skillsHashtag, setSkillHastags] = useState([])
  const [profileData, setProfileData] = useState([])
  const editRecord = () => {
    setEditProfile(!editProfile)
  }
  useEffect(() => {
    setSkillHastags(SkillData)
    setProfileData(ProfileData)
    ProfileData.forEach(item => {
      setSkills(item.skills)
    })
  }, [])
  const [skills, setSkills] = useState([])

  const saveRecord = () => {
    
    setEditProfile(!editProfile)
    const newState = profileData.map(item => {
      return {
        ...item,
       
        skills: skills,
    
      }
    })
    console.log(newState)
    setProfileData(newState)
  }

  const deleteHastag = id => {
    console.log(id)
    setSkillHastags(current =>
      current.filter(item => {
        return item.id !== id
      })
    )
  }
  const addHashtTag = id => {
    skillsHashtag.forEach(item => {
      if (item.id === id) {
        skillArray.push(item.name)
      }
      setSkills(skillArray)
    })
    const newState = skillsHashtag.map(item => {
      if (item.id === id) {
        return {
          ...item,
          status: true
        }
      }
      return item
    })
    console.log(newState)
    setSkillHastags(newState)
  }
  const [open, setOpen] = useState(false)
  const [newHashtag, setNewHashtag] = useState('')
  const updateHashtag = e => setNewHashtag(e.target.value)
  const addNewHashtag = () => {
    const tempObj = {
      id: 1 + Math.random() * (100 - 1),
      name: newHashtag,
      status: false
    }
    console.log(tempObj)
    skillHashArray = skillsHashtag
    skillHashArray.push(tempObj)
    setSkillHastags(skillHashArray)
    setOpen(false)
  }
  const openModal = () => setOpen(!open)
  return (
    <>
      <div class='card'>
        <div class='card-header float-right'>
          <h1>Skills</h1>
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
                    <div class='row'>
                      {item.skills.map(items => (
                        <div class='col-sm-3'>
                          <a href='#' class='btn btn-primary mb-2'>
                            {items}
                          </a>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <>
              <div class='row'>
                <div class='col-sm-9'>
                  <div class='row'>
                    {skillsHashtag?.map(item => (
                      <div class='col-sm-4 mb-3'>
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
                </div>
                <div class='col-sm-3'>
                  <div>
                    <button
                      class='btn btn-primary float-right'
                      onClick={openModal}
                    >
                      Add Skills
                    </button>
                  </div>
                </div>
              </div>
              <div>
                <button
                  class='btn btn-primary float-right'
                  onClick={saveRecord}
                >
                  Save
                </button>
              </div>
            </>
          )}
        </div>
      </div>{' '}
      <Modal isOpen={open} toggle={openModal}>
        <ModalBody>
          {' '}
          <Label>Add Hashtag</Label>
          <Input
            type='text'
            name='hashtag'
            placeholder='Hashtag'
            onChange={updateHashtag}
          />
        </ModalBody>
        <ModalFooter>
          <Button color='secondary' onClick={addNewHashtag}>
            Save
          </Button>
        </ModalFooter>
      </Modal>
    </>
  )
}

export default Skills

import React,{ useEffect, useState } from 'react'
import {
    Button, Input, Label, Modal, ModalBody,
    ModalFooter
} from 'reactstrap'
import './user.css'
var fileArray = []
function Files () {
  const [editProfile, setEditProfile] = useState(false)
  const [possibleFiles, setPossibleFiles] = useState([])
  const[id,setId]=useState(0)

  const editRecord = () => {
    setEditProfile(!editProfile)
  }
  const [profileData, setProfileData] = useState([
     'The first block.docx'
    
  ])
  useEffect(() => {
    profileData.forEach(item => {
      setPossibleFiles(item)
    })
  }, [])
  useEffect(() => {
  setId(0)
  }, [profileData])
  const [files, setFiles] = useState('')

  const [open, setOpen] = useState(false)
  const openModal = () => setOpen(!open)
  const saveRecord = () => {
    setEditProfile(!editProfile)
   setProfileData(possibleFiles)
  }

  const updateFiles = e => {
    console.log(e.target.files)
    setFiles(e.target.files[0].name)
  }

  const addNewFile = () => {
    fileArray = [possibleFiles]
    fileArray.push(files)
    console.log(fileArray)
    setPossibleFiles(fileArray)
    setProfileData(fileArray)
    setOpen(false)
  }
  const deleteFiles=(index)=>{
    fileArray = possibleFiles
    fileArray.splice(index,1)
    console.log(fileArray)
    setPossibleFiles(fileArray)
    setProfileData(fileArray)
    setId(1)
  }
  return (
    <>
      <div class='card'>
        <div class='card-header float-right'>
          <h1>Files</h1>
          <div class='pointer' onClick={editRecord}>
            <i class='icon-pencil ml-3 '></i>
          </div>
        </div>

        <div class='card-body'>
          {!editProfile ? (
            <div class='container'>
              {profileData.map(item => (
                <div class='row'>
                  <div class='col-sm-4'>
                    <p>{item}</p>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div class='row'>
              <div class='col-sm-6'>
                {profileData.map((item,index) => (
                  <div class='row'>
                    <div class='col-sm-6'>
                      <span>{item}</span>
                      <span
                        onClick={() => deleteFiles(index)}
                        class='pointer'
                      >
                        <i class='fa fa-times ml-2' aria-hidden='true'></i>
                      </span>
                    </div>
                  </div>
                ))}
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
                    Add Files
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
          <Label>Add Files</Label>
          <Input
            type='file'
            name='hashtag'
            placeholder='Files'
            onChange={updateFiles}
          />
        </ModalBody>
        <ModalFooter>
          <Button color='secondary' onClick={addNewFile}>
            Save
          </Button>
        </ModalFooter>
      </Modal>
    </>
  )
}

export default Files

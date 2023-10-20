import React,{ useState } from 'react'
import Education from './Education'
import Files from './Files'
import Information from './Information'
import Language from './Language'
import Profile from './Profile'
import Skills from './Skills'
import './user.css'
function BookMark () {
  const [id, setId] = useState('1')
  const setBookMart = id => {
    setId(id)
  }
  return (
    <>
      <div class='card'>
        <div class='card-header float-right'>
          <div class='container'>
            <div class='row'>
              <div
                class={
                  id === '1' ? 'col-sm-2 active pointer' : 'col-sm-2 pointer'
                }
                onClick={() => setBookMart('1')}
              >
                Personal Information
              </div>
              <div
                class={
                  id === '2' ? 'col-sm-2 active pointer' : 'col-sm-2 pointer'
                }
                onClick={() => setBookMart('2')}
              >
                Job History
              </div>
              <div
                class={
                  id === '3' ? 'col-sm-2 active pointer' : 'col-sm-2 pointer'
                }
                onClick={() => setBookMart('3')}
              >
                Education
              </div>
              <div
                class={
                  id === '4' ? 'col-sm-2 active pointer' : 'col-sm-2 pointer'
                }
                onClick={() => setBookMart('4')}
              >
                Skills
              </div>
              <div
                class={
                  id === '5' ? 'col-sm-2 active pointer' : 'col-sm-2 pointer'
                }
                onClick={() => setBookMart('5')}
              >
                Language
              </div>
              <div
                class={
                  id === '6' ? 'col-sm-2 active pointer' : 'col-sm-2 pointer'
                }
                onClick={() => setBookMart('6')}
              >
                Files
              </div>
            </div>
          </div>
        </div>
      </div>
      {id === '1' ? (
        <Profile />
      ) : id === '2' ? (
        <Information />
      ) : id === '3' ? (
        <Education />
      ) : id === '4' ? (
        <Skills />
      ) : id === '5' ? (
        <Language />
      ) : (
       <Files/>
      )}
    </>
  )
}

export default BookMark

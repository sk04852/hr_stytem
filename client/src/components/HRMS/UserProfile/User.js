import React,{ Component } from 'react'
import { connect } from 'react-redux'
import Actions from './Actions'
import BookMark from './Bookmark'
import JobBlock from './JobBlock'
import Personal from './Personal'
import './user.css'
class UserProfile extends Component {
  render () {
    const { fixNavbar } = this.props
    return (
      <>
        <Personal />
        <BookMark />
        <Actions></Actions>
        <JobBlock />
      </>
    )
  }
}
const mapStateToProps = state => ({
  fixNavbar: state.settings.isFixNavbar
})

const mapDispatchToProps = dispatch => ({})
export default connect(mapStateToProps, mapDispatchToProps)(UserProfile)

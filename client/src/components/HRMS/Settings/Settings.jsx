import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import Profile from "./Profile";
import ChangePassword from "./ChangePassword";
import EmailTemplates from "./EmailTemplate/EmailTemplates";
import Permissions from "./Permissions";
import Users from "./Users";
import Roles from "./Roles";
import { getUserProfile } from "../../../redux/actions/usersAction";
import { getTimezone } from "../../../redux/actions/timezoneActions";
import { getRoles } from "../../../redux/actions/rolesActions";
import { useHistory, useLocation } from "react-router-dom";
import { useTranslation } from "react-i18next";

const Settings = (props) => {
  const { fixNavbar } = props;
  const dispatch = useDispatch();
  const history = useHistory();
  const location = useLocation();

  const { t } = useTranslation();

  const [tabsId, setTabsId] = useState("1");
  const [data, setData] = useState("");
  const [userPermission, setUserPermission] = useState([]);
  const [timezone, setTimezone] = useState([]);
  const [userRoles, setUserRoles] = useState([]);

  const setBookMart = (tabID) => {
    history.push(`/jobportal-settings/${tabID}`);
    setTabsId(tabID);
  };

  useEffect(() => {
    const userProfile = async () => {
      try {
        await dispatch(getUserProfile());
      } catch (error) {
        throw error;
      }
    };
    userProfile();
  }, []);

  useEffect(() => {
    dispatch(getTimezone());
  }, []);

  useEffect(() => {
    dispatch(getRoles());
  }, []);

  const getUserProfileSelector = useSelector(
    (state) => state.users.user_profile.data
  );
  const getTimezoneSelector = useSelector(
    (state) => state.timezone.timezone.data
  );
  const getRolesSelector = useSelector((state) => state.roles.roles.data);

  useEffect(() => {
    if (getUserProfileSelector) {
      setData(getUserProfileSelector);
      setUserPermission(getUserProfileSelector.permissions);
    }

    if (getTimezoneSelector) {
      setTimezone(getTimezoneSelector.Timezones);
    }

    if (getRolesSelector) {
      setUserRoles(getRolesSelector.Roles.data);
    }
  }, [getUserProfileSelector, getTimezoneSelector, getRolesSelector]);

  const RenderSettings = () => {
    return (
      <div className="card mt-2">
        <div className="card-header float-right">
          <div className="container">
            <div className="row">
              <div
                className={
                  location.pathname === `/jobportal-settings/1`
                    ? "col-sm-2 active pointer"
                    : "col-sm-2 pointer"
                }
                onClick={() => setBookMart("1")}
              >
                {t("settings.personalData.heading")}
              </div>
              <div
                className={
                  location.pathname === `/jobportal-settings/2`
                    ? "col-sm-2 active pointer"
                    : "col-sm-2 pointer"
                }
                onClick={() => setBookMart("2")}
              >
                {t("settings.changePassword.heading")}
              </div>
              <div
                className={
                  location.pathname === `/jobportal-settings/3`
                    ? "col-sm-2 active pointer"
                    : "col-sm-2 pointer"
                }
                onClick={() => setBookMart("3")}
              >
                {t("settings.emailTemplates.heading")}
              </div>
              {userPermission.includes("Can-Add-New-Permissions") ? (
                <div
                  className={
                    location.pathname === `/jobportal-settings/4`
                      ? "col-sm-2 active pointer"
                      : "col-sm-2 pointer"
                  }
                  onClick={() => setBookMart("4")}
                >
                  {t("settings.userPermissions.heading")}
                </div>
              ) : null}
              {userPermission.includes("Can-View-Users") ? (
                <div
                  className={
                    location.pathname === `/jobportal-settings/5`
                      ? "col-sm-2 active pointer"
                      : "col-sm-2 pointer"
                  }
                  onClick={() => setBookMart("5")}
                >
                  {t("settings.users.heading")}
                </div>
              ) : null}
              {userPermission.includes("Can-View-Roles") ? (
                <div
                  className={
                    location.pathname === `/jobportal-settings/6`
                      ? "col-sm-2 active pointer"
                      : "col-sm-2 pointer"
                  }
                  onClick={() => setBookMart("6")}
                >
                  {t("settings.roles.heading")}
                </div>
              ) : null}
              {location.pathname === `/jobportal-settings/1` ? (
                <Profile
                  userProfileData={data}
                  timezone={timezone}
                  userRoles={userRoles}
                />
              ) : location.pathname === `/jobportal-settings/2` ? (
                <ChangePassword />
              ) : location.pathname === `/jobportal-settings/3` ? (
                <EmailTemplates
                  userPermission={userPermission}
                  tabsId={tabsId}
                  setBookMart={setBookMart}
                />
              ) : location.pathname === `/jobportal-settings/4` ? (
                <Permissions userPermission={userPermission} />
              ) : location.pathname === `/jobportal-settings/5` ? (
                <Users permissions={userPermission} />
              ) : (
                <Roles userRoles={userRoles} />
              )}
            </div>
          </div>
        </div>
      </div>
    );
  };

  return (
    <>
      <div className={`section-body ${fixNavbar ? "marginTop" : ""} mt-3`}>
        <div className="container-fluid">
          <RenderSettings />
        </div>
      </div>
    </>
  );
};
export default Settings;

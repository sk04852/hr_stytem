import React, { useCallback, useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { NavLink, useHistory } from "react-router-dom";
import { logoutUser } from "../../redux/actions/usersAction";
// Import as a module in your JS
import {
  getAllNotifications,
  getUnreadNotificationsCount,
  markAllNotificationRead,
  markNotificationRead,
} from "../../redux/actions/taskManagementActions";
import { DataLoader } from "../constants/loaders"; // ES2015
// var Typeahead = require('react-bootstrap-typeahead').Typeahead; // CommonJS
import { FormGroup } from "reactstrap";
import { CustomErrors } from "../constants/noRecordFound";
import {
  fontSizeAction,
  lineHeightAction,
} from "../../redux/actions/settingsAction";
import { baseURL, storageURL } from "./baseURL";
import axios from "axios";
import { AutoComplete } from "antd";
import { useTranslation } from "react-i18next";
import AvatarImage from "../../assets/images/avatar.jpg";

const Header = (props) => {
  const { fixNavbar, darkHeader } = props;

  const history = useHistory();
  const dispatch = useDispatch();

  const { t, i18n } = useTranslation();

  const [allNotifications, setAllNotifications] = useState([]);
  const [notificationCounts, setNotificationsCount] = useState("");
  const [notificationsIds, setNotificationsIds] = useState([]);
  const [globalSearchData, setGlobalSearchData] = useState([]);
  const [options, setOptions] = useState([]);
  const [lang, setLang] = useState("");

  const fetchAllNotifications = async () => {
    try {
      const response = await dispatch(getAllNotifications());
    } catch (error) {
      throw error;
    }
  };

  const fetchUnreadNotification = async () => {
    try {
      await dispatch(getUnreadNotificationsCount());
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchAllNotifications();
    fetchUnreadNotification();
  }, [notificationCounts]);

  const taskSelector = useSelector((state) => state.tasks);
  const allNotificationsSelector = useSelector(
    (state) => state.tasks.all_notifications.notifications
  );
  const notificationsCountSelector = useSelector(
    (state) => state.tasks.notifications_count.unread_notifications
  );

  useEffect(() => {
    if (allNotificationsSelector) {
      setAllNotifications(allNotificationsSelector);

      let ids = [];
      allNotificationsSelector.forEach((items) => {
        ids.push(items.id);
      });
      setNotificationsIds(ids);
    }

    if (notificationsCountSelector) {
      setNotificationsCount(notificationsCountSelector);
    }
  }, [allNotificationsSelector, notificationsCountSelector]);

  const handleLogout = () => {
    dispatch(logoutUser(history));
  };

  const handleMarkNotificationsRead = () => {
    dispatch(markNotificationRead(notificationsIds));
  };

  const handleMarkAllNotification = () => {
    dispatch(
      markAllNotificationRead(fetchAllNotifications, fetchUnreadNotification)
    );
    // fetchUnreadNotification();
  };

  const handleLanguageChange = (lang) => {
    localStorage.setItem("lang", lang);
    i18n.changeLanguage(lang);
  };

  const handleFontSizeChange = (e) => {
    dispatch(fontSizeAction(e.target.value));
  };

  const handleLineHeightChange = (e) => {
    dispatch(lineHeightAction(e.target.value));
  };

  const debounce = (func) => {
    let timer;
    return function (...args) {
      const context = this;
      if (timer) clearTimeout(timer);
      timer = setTimeout(() => {
        timer = null;
        func.apply(context, args);
      }, 500);
    };
  };

  const handleGlobalSearch = (e) => {
    let searchValue = e;
    let searchOptions = [];
    if (searchValue.length > 0) {
      const token = localStorage.getItem("token");
      axios
        .post(
          `${baseURL}api/global-search`,
          { search_query: searchValue },
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        )
        .then((response) => {
          setGlobalSearchData(response.data.data);
          if (response.data.data.length > 0) {
            response.data.data.map((item) => {
              searchOptions.push({
                value: item.name,
                label: item.name,
                path: item.path,
                id: item.id,
              });
            });
            setOptions(searchOptions);
          }
        });
    }
  };

  const onSelect = (data, option) => {
    let id = option.id;
    let path = option.path;
    setOptions([]);
    setGlobalSearchData([]);

    if (path === "/hr-candidate/view/") {
      history.push(`/hr-candidate/view/${id}/1`);
    } else if (path === "/hr-companies/view/") {
      history.push(`/hr-companies/view/${id}`);
    } else if (path === "/hr-jobs/view/") {
      history.push(`/hr-jobs/view/${id}`);
    }
  };

  const optimizedGlobalSearchFn = useCallback(debounce(handleGlobalSearch), []);

  return (
    <div>
      <div
        id="page_top"
        // className={isFixNavbar ? "sticky-top" : "" + this.props.dataFromParent === 'dark' ? 'section-body top_dark' : 'section-body'}
        className={`section-body ${fixNavbar ? "sticky-top" : ""} ${
          darkHeader ? "top_dark" : ""
        }`}
      >
        <div className="container-fluid">
          <div className="page-header">
            <div className="left">
              <h1 className="page-title">{props.dataFromSubParent}</h1>
              <select className="custom-select">
                <option>Year</option>
                <option>Month</option>
                <option>Week</option>
              </select>
              <div className="input-group xs-hide">
                <AutoComplete
                  options={options}
                  style={{ width: 200 }}
                  onSelect={onSelect}
                  onSearch={optimizedGlobalSearchFn}
                  placeholder="Search..."
                  notFoundContent={"No Search Found"}
                  allowClear={true}
                />
              </div>
            </div>
            <div className="right">
              <ul className="nav nav-pills">
                <li className="nav-item dropdown">
                  <a
                    className="nav-link dropdown-toggle"
                    data-toggle="dropdown"
                    role="button"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    {t("header.language")}
                  </a>
                  <div className="dropdown-menu">
                    <form>
                      {[
                        {
                          id: 1,
                          fieldID: "english",
                          fieldName: "en-language",
                          fieldValue: "en",
                          image: "../assets/images/flags/us.svg",
                          altText: "us flag",
                          fieldType: "hidden",
                          languageName: "English",
                        },
                        {
                          id: 2,
                          fieldID: "estonian",
                          fieldName: "et-language",
                          fieldValue: "et",
                          image: "../assets/images/flags/ee.svg",
                          altText: "ee flag",
                          fieldType: "hidden",
                          languageName: "Estonian",
                        },
                      ].map((langauge, index) => (
                        <a
                          key={index}
                          className="dropdown-item"
                          onClick={() => {
                            handleLanguageChange(langauge.fieldValue);
                          }}
                        >
                          <img
                            className="w20 mr-2"
                            src={langauge.image}
                            alt={langauge.altText}
                          />
                          {langauge.languageName}
                        </a>
                      ))}
                    </form>
                  </div>
                </li>
                <li className="nav-item dropdown">
                  <a
                    className="nav-link dropdown-toggle"
                    data-toggle="dropdown"
                    role="button"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    {t("header.accessibility")}
                  </a>
                  <div className="dropdown-menu custom-accessibility-dropdown">
                    <div className={"p-2 w-100 d-flex"}>
                      <div>
                        <h6>Text Size</h6>
                        <form className={"border p-3"}>
                          {[
                            {
                              id: 1,
                              fieldID: "default",
                              name: "default_radio",
                              value: "Default",
                            },
                            {
                              id: 2,
                              fieldID: "large",
                              name: "default_radio",
                              value: "Large",
                            },
                            {
                              id: 3,
                              fieldID: "extra-large",
                              name: "default_radio",
                              value: "Extra Large",
                            },
                          ].map((fieldItem, index) => (
                            <FormGroup check key={index}>
                              <input
                                type={"radio"}
                                id={fieldItem.fieldID}
                                name={fieldItem.name}
                                value={fieldItem.value}
                                onChange={(e) =>
                                  handleFontSizeChange(e, fieldItem.fieldID)
                                }
                              />{" "}
                              <label htmlFor={fieldItem.fieldID}>
                                {fieldItem.value}
                              </label>
                            </FormGroup>
                          ))}
                        </form>
                      </div>
                      <div className={"ml-5"}>
                        <h6>Line Height</h6>
                        <form className={"border p-3"}>
                          {[
                            {
                              id: 1,
                              fieldID: "default",
                              name: "default_radio",
                              value: "Default",
                            },
                            {
                              id: 2,
                              fieldID: "line-height-2x",
                              name: "default_radio",
                              value: "2x",
                            },
                            {
                              id: 3,
                              fieldID: "line-height-4x",
                              name: "default_radio",
                              value: "4x",
                            },
                          ].map((fieldItem, index) => (
                            <FormGroup check key={index}>
                              <input
                                type={"radio"}
                                id={fieldItem.fieldID}
                                name={fieldItem.name}
                                value={fieldItem.value}
                                onChange={(e) => handleLineHeightChange(e)}
                              />{" "}
                              <label htmlFor={fieldItem.fieldID}>
                                {fieldItem.value}
                              </label>
                            </FormGroup>
                          ))}
                        </form>
                      </div>
                    </div>
                  </div>
                </li>
                {/* <li className="nav-item dropdown">
                  <a
                    className="nav-link dropdown-toggle"
                    data-toggle="dropdown"
                    role="button"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    {t("header.projects")}
                  </a>
                  <div className="dropdown-menu">
                    <a className="dropdown-item">Graphics Design</a>
                    <a className="dropdown-item">Angular Admin</a>
                    <a className="dropdown-item">PSD to HTML</a>
                    <a className="dropdown-item">iOs App Development</a>
                    <div className="dropdown-divider" />
                    <a className="dropdown-item">Home Development</a>
                    <a className="dropdown-item">New Blog post</a>
                  </div>
                </li> */}
              </ul>
              <div className="notification d-flex">
                <div className="dropdown d-flex">
                  <a
                    href="/#"
                    className="nav-link icon d-none d-md-flex btn btn-default btn-icon ml-1"
                    data-toggle="dropdown"
                  >
                    <i className="fa fa-envelope" />
                  </a>
                  <div className="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                    <ul className="right_chat list-unstyled w250 p-0">
                      <li className="online">
                        <a href="fake_url">
                          <div className="media">
                            <img
                              className="media-object "
                              src="../assets/images/xs/avatar4.jpg"
                              alt="fake_url"
                            />
                            <div className="media-body">
                              <span className="name">Donald Gardner</span>
                              <span className="message">Designer, Blogger</span>
                              <span className="badge badge-outline status" />
                            </div>
                          </div>
                        </a>
                      </li>
                      <li className="online">
                        <a href="fake_url">
                          <div className="media">
                            <img
                              className="media-object "
                              src="../assets/images/xs/avatar5.jpg"
                              alt="fake_url"
                            />
                            <div className="media-body">
                              <span className="name">Wendy Keen</span>
                              <span className="message">Java Developer</span>
                              <span className="badge badge-outline status" />
                            </div>
                          </div>
                        </a>
                      </li>
                      <li className="offline">
                        <a href="fake_url">
                          <div className="media">
                            <img
                              className="media-object "
                              src="../assets/images/xs/avatar2.jpg"
                              alt="fake_url"
                            />
                            <div className="media-body">
                              <span className="name">Matt Rosales</span>
                              <span className="message">CEO, Epic Theme</span>
                              <span className="badge badge-outline status" />
                            </div>
                          </div>
                        </a>
                      </li>
                      <li className="online">
                        <a href="fake_url">
                          <div className="media">
                            <img
                              className="media-object "
                              src="../assets/images/xs/avatar3.jpg"
                              alt="fake_url"
                            />
                            <div className="media-body">
                              <span className="name">Phillip Smith</span>
                              <span className="message">
                                Writter, Mag Editor
                              </span>
                              <span className="badge badge-outline status" />
                            </div>
                          </div>
                        </a>
                      </li>
                    </ul>
                    <div className="dropdown-divider" />
                    <a className="dropdown-item text-center text-muted-dark readall">
                      Mark all as read
                    </a>
                  </div>
                </div>
                <div className="dropdown d-flex">
                  <a
                    href="/#"
                    className="nav-link icon d-none d-md-flex btn btn-default btn-icon ml-1"
                    data-toggle="dropdown"
                  >
                    {/* // dispatch(markNotificationRead(notificationsIds)) */}
                    <i
                      className="fa fa-bell"
                      onClick={() => handleMarkNotificationsRead()}
                    />
                    {notificationsCountSelector > 0 ? (
                      <span
                        className="badge badge-primary nav-unread"
                        style={{ top: "-5px", right: "-1px" }}
                      >
                        {notificationsCountSelector}
                      </span>
                    ) : null}
                  </a>
                  <div className="dropdown-menu dropdown-menu-right dropdown-menu-arrow pt-0">
                    <ul className="list-unstyled feeds_widget">
                      {!taskSelector.isLoading ? (
                        <>
                          {allNotifications && allNotifications.length > 0 ? (
                            allNotifications.map((items, index) => {
                              if (items.read_at === null) {
                                return (
                                  <li
                                    key={index}
                                    style={{ background: "#c0d6e4" }}
                                  >
                                    <div className="feeds-left mr-3">
                                      <img
                                        src={
                                          items.image
                                            ? items.image
                                            : AvatarImage
                                        }
                                        alt={"image"}
                                        className="rounded mr-1"
                                        width={30}
                                        height={30}
                                      />
                                    </div>
                                    <div className="feeds-body">
                                      <h4 className="title text-danger">
                                        {items.title}
                                        <small className="float-right text-muted"></small>
                                      </h4>
                                      <span
                                        dangerouslySetInnerHTML={{
                                          __html: items.description,
                                        }}
                                      />
                                      {/* <small>{items.description}</small> */}
                                    </div>
                                  </li>
                                );
                              } else {
                                return (
                                  <li key={index}>
                                    <div className="feeds-left mr-3">
                                      {/*<i className="fa fa-check" />*/}
                                      <img
                                        src={
                                          items.image
                                            ? items.image
                                            : AvatarImage
                                        }
                                        alt={"image"}
                                        className="rounded mr-1"
                                        width={30}
                                        height={30}
                                      />
                                    </div>
                                    <div className="feeds-body">
                                      <h4 className="title text-danger">
                                        {items.title}
                                        <small className="float-right text-muted">
                                          {/*{items.created_at}*/}
                                        </small>
                                      </h4>
                                      <span
                                        dangerouslySetInnerHTML={{
                                          __html: items.description,
                                        }}
                                      />
                                      {/* <small>{items.description}</small> */}
                                    </div>
                                  </li>
                                );
                              }
                            })
                          ) : (
                            <CustomErrors message={"Empty Notifications"} />
                          )}
                        </>
                      ) : (
                        <div className={"d-flex justify-content-center"}>
                          <DataLoader />
                        </div>
                      )}
                    </ul>
                    <div className="dropdown-divider" />
                    <div className={"w-100 d-flex justify-content-center"}>
                      {allNotifications.length > 0 && (
                        <a
                          href={"#"}
                          className="dropdown-item text-center text-muted-dark"
                          onClick={(e) => {
                            e.stopPropagation();
                            dispatch(
                              getAllNotifications(
                                taskSelector.all_notifications.offset
                              )
                            );
                          }}
                        >
                          {t("header.notifications.loadMore")}
                        </a>
                      )}
                      <a
                        href={"#"}
                        className="dropdown-item text-center text-muted-dark"
                        onClick={(e) => {
                          e.stopPropagation();
                          handleMarkAllNotification();
                        }}
                      >
                        {t("header.notifications.markAllAsRead")}
                      </a>
                    </div>
                  </div>
                </div>
                <div className="dropdown d-flex">
                  <a
                    href="/#"
                    className="nav-link icon d-none d-md-flex btn btn-default btn-icon ml-1"
                    data-toggle="dropdown"
                  >
                    <i className="fa fa-user" />
                  </a>
                  <div className="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                    <NavLink to="/profile" className="dropdown-item">
                      <i className="dropdown-icon fe fe-user" />{" "}
                      {t("header.profileSettings.profile")}
                    </NavLink>
                    <a className="dropdown-item">
                      <i className="dropdown-icon fe fe-settings" />{" "}
                      {t("header.profileSettings.settings")}
                    </a>
                    <a className="dropdown-item">
                      <span className="float-right">
                        <span className="badge badge-primary">6</span>
                      </span>
                      <i className="dropdown-icon fe fe-mail" />{" "}
                      {t("header.profileSettings.inbox")}
                    </a>
                    <a className="dropdown-item">
                      <i className="dropdown-icon fe fe-send" />{" "}
                      {t("header.profileSettings.messages")}
                    </a>
                    <div className="dropdown-divider" />
                    <a className="dropdown-item">
                      <i className="dropdown-icon fe fe-help-circle" />{" "}
                      {t("header.profileSettings.needHelp")}?
                    </a>
                    <NavLink
                      to={"#"}
                      className="dropdown-item bg-none"
                      onClick={() => handleLogout()}
                    >
                      <i className="dropdown-icon fe fe-log-out" />
                      {t("header.profileSettings.signOut")}
                    </NavLink>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Header;

import React, { useEffect, useState } from "react";
import "../../Candidates/candidate.css";
import { Card, CardBody, CardHeader, Label } from "reactstrap";
import { useDispatch, useSelector } from "react-redux";
import { getAllUsers } from "../../../../redux/actions/usersAction";
import { useHistory } from "react-router-dom";
import { useTranslation } from "react-i18next";

const Permissions = (props) => {
  const { userPermission } = props;
  const dispatch = useDispatch();
  const history = useHistory();

  const { t } = useTranslation();

  const [allUsers, setAllUsers] = useState([]);

  useEffect(() => {
    const fetchUsers = async () => {
      try {
        const response = await dispatch(getAllUsers());
        setAllUsers(response.payload.data.Users.data);
      } catch (e) {}
    };
    fetchUsers();
  }, []);

  const settingsSelector = useSelector((state) => state.settings);

  const handleChange = (e) => {
    const userID = e.target.value;
    history.push({
      pathname: `/jobportal-settings/permissions/${userID}`,
      state: userPermission,
    });
  };

  return (
    <Card>
      <CardHeader>
        <h1>{t("settings.userPermissions.heading")}</h1>
      </CardHeader>
      <CardBody>
        <Label
          style={{
            fontSize: `${
              settingsSelector.FontSize === "Large"
                ? "large"
                : settingsSelector.FontSize === "Extra Large"
                ? "x-large"
                : "0.875rem"
            }`,
          }}
        >
          {t("settings.userPermissions.dropdownHeading")}
        </Label>
        <select
          className="custom-select"
          onChange={(e) => handleChange(e)}
          style={{
            fontSize: `${
              settingsSelector.FontSize === "Large"
                ? "large"
                : settingsSelector.FontSize === "Extra Large"
                ? "x-large"
                : "14px"
            }`,
          }}
        >
          <option value={"none"}>-- Vali kasutaja --</option>
          {allUsers.map((items, index) => (
            <option key={index} value={items.userpr_id}>
              {items.name}
            </option>
          ))}
        </select>
      </CardBody>
    </Card>
  );
};

export default Permissions;

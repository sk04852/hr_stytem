import React, { Component } from "react";
import { Route, Redirect } from "react-router-dom";
import { useSelector } from "react-redux";

export const PrivateRoute = ({ component: Component, ...rest }) => {
  const authenticated = useSelector((state) => state.users.isAuthenticated);

  return (
    <Route
      {...rest}
      render={(props) => {
        if (!authenticated) {
          return <Redirect to={"/"} />;
        }
        return <Component {...props} />;
      }}
    />
  );
};

import { useState } from "react";
import Mfa from "./Mfa.tsx";
import axios from 'axios';
import jQuery from "jquery";

const api = axios.create({
  baseURL: "http://localhost:3000/graphql",
  headers: {'Accept': 'application/json',
            'Content-Type': 'application/json'}
})

export default function Login() {
  const [username, setUsername] = useState<string>('');
  const [password, setPassword] = useState<string>('');
  const [message, setMessage] = useState<string>('');
  const [isDisabled, setIsDisabled] = useState(false);

  const submitLogin = async (event: any) => {
    event.preventDefault();
    setMessage('Please wait...');
    setIsDisabled(true);

    const loginQuery = {
      query: `
        mutation userLogin($username: String!, $password: String!) {
          signinUser(username: $username, password: $password) {
            token
            user {
              id firstname lastname email mobile username isactivated, isblocked userpic qrcodeurl
            }
          }
        }
      `,
      variables: { username, password }
    };

    try {

      const res = await api.post('', loginQuery);
      const result = res.data.data?.signinUser;
      const userData = result?.user;
      const token = result?.token;
      
      if (!userData) {
        throw new Error(res.data.errors?.[0]?.message || "Login failed");
      }

      window.sessionStorage.setItem('USERID', userData.id);
      window.sessionStorage.setItem("USERNAME", userData.username)
      let userpic: string = `http://localhost:3000/users/${userData.userpic}`;
      window.sessionStorage.setItem("USERPIC", userpic);
      window.sessionStorage.setItem('TOKEN', token);

      if (userData.qrcodeurl) {
        jQuery("#mfaModal").trigger('click');
      } else {
        location.reload();
      }
    } catch (error: any) {
      setMessage(error.message || "An error occurred");
      setTimeout(() => setMessage(''), 3000);
    } finally {
      setIsDisabled(false);
    }
  };

  const closeLogin = (event: any) => {
    event.preventDefault();
    setIsDisabled(false);
    setMessage('');
    setUsername('');
    setPassword('');
  }

  return (
    <>
<div className="modal fade" id="staticLogin" data-bs-backdrop="static" data-bs-keyboard="false" tabIndex={-1} aria-labelledby="staticLoginLabel" aria-hidden="true">
  <div className="modal-dialog modal-sm modal-dialog-centered">
    <div className="modal-content">
      <div className="modal-header bg-violet">
        <h1 className="modal-title text-white fs-5" id="staticLoginLabel">User's Login</h1>
        <button onClick={closeLogin} type="button" className="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div className="modal-body">
        <form onSubmit={submitLogin} autoComplete="off">
        <div className="mb-3">
          <input type="text" required value={username} onChange={e => setUsername(e.target.value)} className="form-control border-secondary border-emboss" disabled={isDisabled} autoComplete='off' placeholder="enter Username"/>
        </div>          
        <div className="mb-3">
          <input type="password" required value={password} onChange={e => setPassword(e.target.value)} className="form-control border-secondary border-emboss" disabled={isDisabled} autoComplete='off' placeholder="enter Password"/>
        </div>          
        <div className="mb-3">
          <button type="submit" className="btn btn-violet text-white mx-2" disabled={isDisabled}>login</button>
          <button id="loginReset" onClick={closeLogin} type="reset" className="btn btn-violet text-white">reset</button>
          <button id="mfaModal" type="button" className="btn btn-warning d-none" data-bs-toggle="modal" data-bs-target="#staticMfa">mfa</button>

          </div>
        </form>
      </div>
      <div className="modal-footer">
        <div className="w-100 text-danger">{message}</div>
      </div>
    </div>
  </div>
</div>    
<Mfa/>
</>
  )
}

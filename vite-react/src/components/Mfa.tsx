import { useState } from "react"
import jQuery from "jquery";
import axios from 'axios';

const api = axios.create({
   baseURL: "http://localhost:3000/graphql",
   headers: {'Accept': 'application/json',
             'Content-Type': 'application/json'}
})

export default function Mfa() {
  const [otp, setOtp] = useState<string>('');
  const [message, setMessage] = useState<string>('');

  const submitMfa = async (event: any) => {
    event.preventDefault();

    const userid = sessionStorage.getItem('USERID') || '';
    // const token = sessionStorage.getItem('TOKEN');
    setMessage('please wait..');
        const otpQuery = {
            query: `
                mutation VerifyMFA(
                    $id: Int!, 
                    $otp: String!) {
                    mfaVerification(id: $id, otp: $otp) {
                        message
                        id
                        username
                    }
                }
            `,
            variables: { id: parseInt(userid), otp: otp}
        };

        try {
            const res = await api.post('', otpQuery);
            if (res.data.errors) {
                setMessage(res.data.errors[0].message);
                setTimeout(() => {
                    setMessage('');
                }, 3000);
                return;
            }

            const result = res.data.data?.mfaVerification;
            if (result?.message) {
                setMessage(result.message);
                sessionStorage.setItem("USERNAME", result.username);                
                setTimeout(() => {
                    setMessage('');
                    jQuery("#reset").trigger('click');
                    jQuery("#mfaclose").trigger('click');
                    location.reload();
                }, 3000);
            }
        } catch (error: any) {
            const errorMsg = error.response?.data?.errors?.[0]?.message || error.message;
            setMessage(errorMsg);
            setTimeout(() => setMessage(''), 3000);
        }
  }

  const closeMfa = (event: any) => {
    event.preventDefault();
    setMessage('');
    setOtp('');
    sessionStorage.removeItem('USERID');
    sessionStorage.removeItem('USERNAME');
    sessionStorage.removeItem('USERPIC');
    sessionStorage.removeItem('TOKEN');
    location.reload();
  }

  return (
    <div className="modal fade" id="staticMfa" data-bs-backdrop="static" data-bs-keyboard="false" tabIndex={-1} aria-labelledby="staticMfaLabel" aria-hidden="true">
      <div className="modal-dialog modal-sm modal-dialog-centered">
        <div className="modal-content">
          <div className="modal-header bg-info">
            <div className="modal-title fs-5 text-dark" id="staticMfaLabel">Multi-Factor Authenticator</div>
            <button onClick={closeMfa} type="button" className="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            <button id="mfaclose" type="button" className="btn-close d-none" data-bs-dismiss="modal" aria-label="Close"></button>

          </div>
          <div className="modal-body">
          <form onSubmit={submitMfa} autoComplete="off">
            <div className="mb-3">
              <input type="text" required value={otp} onChange={e => setOtp(e.target.value)} className="form-control border-dark" id="otp" placeholder="enter 6-digin OTP code"/>
            </div>          
            <div className="mb-3">
              <button type="submit" className="btn btn-info mx-2 text-dark">submit</button>
              <button type="reset" className="btn btn-info text-dark">reset</button>
            </div>
          </form>            
          </div>
          <div className="modal-footer">
            <div className="w-100 text-center text-danger">{message}</div>
          </div>
        </div>
      </div>
    </div>    
  )
}
        

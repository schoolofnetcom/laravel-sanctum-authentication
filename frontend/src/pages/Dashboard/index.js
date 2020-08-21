import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';

import api from '../../services/api';

const Login = () => {

  const [users, setUsers] = useState([]);

  const listUsers = () => {
    api.get('api/users').then((response) => {
      setUsers(response.data);
    })
  }

  // useEffect(() => {
  //   const token = '1|lZi8Ve2gPuuslL6BHSw5IlAxfUl1ONBUPs7vqD50lPP3pPZ6gl7h0yvx7R1Mbek7oeS1xphhKPdwleHb';
  //   const config = {
  //       headers: { Authorization: `Bearer ${token}` }
  //   };

  //   (async () => {
  //     const {data} = await api.get('/api/users', config);
  //     setUsers(data);
  //   })();

  // }, []);

  return (
    <>
      <div className='btn'>
        <Link to='/'>Login</Link>
      </div>
      <h1>Bem vindo ao dashboard!!!</h1>
      <button onClick={listUsers} className='btn'>Listar usuários</button>
      <ul>
        {
          users.map(user => (
            <li key={user.id}>{user.name}</li>
          ))
        }
      </ul>
    </>
  )
}
  

export default Login;
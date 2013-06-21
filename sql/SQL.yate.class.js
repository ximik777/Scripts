/*

  MySql, PgSql db class for Yate.
  Author: Grigoriy Stratov 
	Docs: https://github.com/ximik777/Scripts/blob/master/sql/README.md

*/

db_account = 'yate';

db = {
    'connect':null,
    'account':null, // or global db_account in config file
    'types':['pgsql', 'mysql'],
    'db_type': 0,
    'last_insert_id':['SELECT LASTVAL()','LAST_INSERT_ID()'],
    'query':function(q, a)
    {
        db.connect = null;
        db.connect = new Message("database");
        if(db.account==null) db.account = db_account;
        db.connect.account = db.account;
        db.connect.query = db.query_replase(q, a);
        if (!db.connect.dispatch()) return false;
        if (db.connect.error) {
            Engine.debug('SQL ' + db.connect.error);
            return false;
        }
        return true;
    },
    'query_replase': function(q, a)
    {
        if (a) {
            if (typeof(a) !== 'object') return db.jtreplace(q, a);

            for (var i = 0; i < a.length; i++) {
                q = db.jtreplace(q, a[i]);
            }
        }
        return q;
    },
    'jtreplace': function(q, a)
    {
        var list = [];
        list.push(q.substr(0, q.indexOf('$')), "'" + a + "'", q.substr(q.indexOf('$') + 1, q.length));
        return list.join('');
    },
    'get_array_list':function(q, a)
    {
        if (!db.query(q, a)) return false;
        var rows = [];
        for (var r = 0; r < db.connect.rows; r++) {
            rows.push(db.connect.getRow(r));
        }
        return rows;
    },
    'get_one_line_assoc':function(q, a)
    {
        if (!db.query(q, a)) return false;
        return db.connect.getRow(0);
    },
    'get_value_query':function(q, a)
    {
        if (!db.query(q, a)) return false;
        if (db.connect.rows == 1 && db.connect.columns == 1) return db.connect.getResult(0, 0);
        return false;
    },
    'getKeyValArray': function(q, a)
    {
        if (!db.query(q, a)) return false;
        var rows = {};
        for (var r = 0; r < db.connect.rows; r++) {
            rows[db.connect.getResult(r, 0)] = db.connect.getResult(r, 1);
        }
        return rows;
    },
    'get_assoc_column': function(q, a)
    {
        if (!db.query(q, a)) return false;
        var rows = [];
        for (var r = 0; r < db.connect.rows; r++) {
            rows.push(db.connect.getResult(r, 0));
        }
        return rows;
    },
    'query_insert':function(q, a)
    {
        q = q + ';'+ db.last_insert_id[db.type];
        if (!db.query(q, a)) return false;
        if (db.connect.rows == 1 && db.connect.columns == 1) return db.connect.getResult(0, 0);
        return true;
    }
};
